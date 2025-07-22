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
 *
 * Contentor 'getUpdates' api endpoint service
 */
class ProcessUpdates
{
    /**
     * Logger instance
     *
     * @var Logger
     */
    private $logger;

    /**
     * Content update handler list
     *
     * @var HandlerList
     */
    private $handlerList;

    /**
     * Get updates gateway
     *
     * @var GetUpdates
     */
    private $getUpdatesGateway;

    /**
     * Entity resolver
     *
     * @var EntityResolver
     */
    private $entityResolver;

    /**
     * Set import state gateway
     *
     * @var SetImportState
     */
    private $setImportStateGateway;

    /**
     * PSR logger interface
     *
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
     * Get and process a chunk of updates, returning the last state change seen
     *
     * @param string $date
     * @return array
     */
    public function execute($date): array
    {
        $result = [];
        try {
            // get first 20 updates
            $updates = $this->getUpdatesGateway->execute($date);
            $updates_data = $updates['data'];
            $lastStateChangeSeen = $date;

             // pagination through updates if there are more than 1 page (or more than 20 updates)
            if ($updates['pagination']['pages'] > 1) {
                for ($page = 2; $page <= $updates['pagination']['pages']; $page++) {
                    $partial_updates = $this->getUpdatesGateway->execute($date, $page);
                    $updates_data = array_merge($updates_data, $partial_updates['data']);
                }
            }
            
            $count = 0;
            foreach ($updates_data as $update) {
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
                'updates' => count($updates_data)
            ];
        } catch (\Exception $e) {
            $this->psrLogger->error($e->getMessage());
        }
        return $result;
    }
}
