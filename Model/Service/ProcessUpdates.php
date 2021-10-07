<?php

namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\ContentUpdate\HandlerList;
use Contentor\LocalizationApi\Model\EntityResolver;
use Contentor\LocalizationApi\Model\Gateway\GetUpdates;
use Contentor\LocalizationApi\Model\Gateway\SetImportState;
use Contentor\LocalizationApi\Model\Logger\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class ProcessUpdates
 * Contentor 'getUpdates' api endpoint service
 */
class ProcessUpdates
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var HandlerList
     */
    private $handlerList;

    /**
     * @var GetUpdates
     */
    private $getUpdatesGateway;

    /**
     * @var EntityResolver
     */
    private $entityResolver;

    /**
     * @var SetImportState
     */
    private $setImportStateGateway;

    /**
     * @var LoggerInterface
     */
    private $psrLogger;

    /**
     * ProcessUpdates constructor.
     * @param Logger $logger
     * @param HandlerList $handlerList
     * @param GetUpdates $getUpdatesGateway
     * @param EntityResolver $entityResolver
     * @param SetImportState $setImportStateGateway
     * @param LoggerInterface $psrLogger
     */
    public function __construct(
        Logger $logger,
        HandlerList $handlerList,
        GetUpdates $getUpdatesGateway,
        EntityResolver $entityResolver,
        SetImportState $setImportStateGateway,
        LoggerInterface $psrLogger
    ) {
        $this->logger = $logger;
        $this->handlerList = $handlerList;
        $this->getUpdatesGateway = $getUpdatesGateway;
        $this->entityResolver = $entityResolver;
        $this->setImportStateGateway = $setImportStateGateway;
        $this->psrLogger = $psrLogger;
    }

    /**
     * Get and process a chunk of updates, returning the last state change
     * seen.
     * @param $date
     * @return array
     */
    public function execute($date): array
    {
        $result = [];
        try {
            $updates = $this->getUpdatesGateway->execute($date);
            $lastStateChangeSeen = $date;
            $count = 0;
            foreach ($updates as $update) {
                // Keep the last state change seen - no matter if the entity exists or not
                $lastStateChangeSeen = $update['lastStateChange'];

                $entity = $this->entityResolver->findByContentorId(
                    $update['id']
                );

                if ($entity == null) {
                    // If entity is not found - continue processing other updates
                    continue;
                }

                $count++;

                //2. find entity handler
                $handler = $this->handlerList->getHandlerByCode($entity->getContentCode());

                //3. process update
                $handler->execute($entity, $update);

                //4 set the importState
                if ($update['state'] == 'completed') {
                    $this->setImportStateGateway->execute($entity->getContentorId(), 'success');
                }
            }

            $this->logger->info(
                'Processed ' . $count
                . ' requests, with the last state change being ' . $lastStateChangeSeen
            );

            $result = [
                'lastStateChange' => $lastStateChangeSeen,
                'updates' => count($updates)
            ];
        } catch (\Exception $e) {
            $this->psrLogger->error($e->getMessage());
        }
        return $result;
    }
}
