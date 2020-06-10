<?php
namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\ContentUpdate\HandlerList;
use Contentor\LocalizationApi\Model\EntityResolver;
use Contentor\LocalizationApi\Model\Gateway\GetUpdates;
use Magento\Framework\Exception\LocalizedException;
use Contentor\LocalizationApi\Model\Gateway\SetImportState;

/**
 * Class ProcessUpades
 * @package Contentor\LocalizationApi\Model\Service
 *
 * Contentor 'getUpdates' api endpoint service
 *
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
     * @var TestConnection
     */
    private $testConnection;

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
     * ProcessUpdates constructor.
     * @param HandlerList $handlerList
     * @param TestConnection $testConnection
     * @param GetUpdates $getUpdatesGateway
     * @param EntityResolver $entityResolver
     * @param SetImportState $setImportStateGateway
     */
    public function __construct(
        Logger $logger,
        HandlerList $handlerList,
        TestConnection $testConnection,
        GetUpdates $getUpdatesGateway,
        EntityResolver $entityResolver,
        SetImportState $setImportStateGateway
    ) {
        $this->logger = $logger;
        $this->handlerList = $handlerList;
        $this->testConnection = $testConnection;
        $this->getUpdatesGateway = $getUpdatesGateway;
        $this->entityResolver = $entityResolver;
        $this->setImportStateGateway = $setImportStateGateway;
    }

    /**
     * Get and process a chunk of updates, returning the last state change
     * seen.
     * @param $date
     * @return array
     * @throws LocalizedException
     */
    public function execute($date)
    {
        //1. get updates
        $updates = $this->getUpdatesGateway->execute($date);
        $lastStateChangeSeen = $date;
        $count = 0;
        foreach ($updates as $update) {
            // Keep the last state change seen - no matter if the entity exists or not
            $lastStateChangeSeen = $update['lastStateChange'];

            $entity = $this->entityResolver->findByContentorId(
                $update['id']
            );

            if($entity == null) {
                // If entity is not found - continue processing other updates
                continue;
            }

            $count++;

            //2. find entity handler
            $handler = $this->handlerList->getHandlerByCode($entity->getContentCode());

            //3. process update
            $handler->execute($entity, $update);

            //4 set the importState
            $this->setImportStateGateway->execute($entity->getContentorId(),'success');
        }

        $this->logger->info('Processed ' . $count . ' requests, with the last state change being ' . $lastStateChangeSeen);

        return [
            'lastStateChange' => $lastStateChangeSeen,
            'updates' => count($updates)
        ];
    }
}
