<?php
namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\ContentUpdate\HandlerList;
use Contentor\LocalizationApi\Model\EntityResolver;
use Contentor\LocalizationApi\Model\Gateway\GetUpdates;
use Magento\Framework\Exception\LocalizedException;

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
     * ProcessUpdates constructor.
     * @param HandlerList $handlerList
     * @param TestConnection $testConnection
     * @param GetUpdates $getUpdatesGateway
     * @param EntityResolver $entityResolver
     */
    public function __construct(
        HandlerList $handlerList,
        TestConnection $testConnection,
        GetUpdates $getUpdatesGateway,
        EntityResolver $entityResolver
    ) {
        $this->handlerList = $handlerList;
        $this->testConnection = $testConnection;
        $this->getUpdatesGateway = $getUpdatesGateway;
        $this->entityResolver = $entityResolver;
    }

    /**
     * Get and process api updates by date
     *
     * @param string $date
     * @throws LocalizedException
     */
    public function execute($date)
    {
        //1. get updates
        $updates = $this->getUpdatesGateway->execute($date);
        foreach ($updates as $update) {
            $entity = $this->entityResolver->findByContentorId(
                $update['id']
            );

            if($entity == null) {
                // If entity is not found - continue processing other updates
                continue;
            }

            //2. find entity handler
            $handler = $this->handlerList->getHandlerByCode($entity->getContentCode());

            //3. process update
            $handler->execute($entity, $update);
        }
    }
}
