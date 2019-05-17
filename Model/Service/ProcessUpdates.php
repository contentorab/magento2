<?php
namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Model\ContentUpdate\HandlerList;
use Contentor\LocalizationApi\Model\EntityResolver;
use Contentor\LocalizationApi\Model\Gateway\GetUpdates;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ProcessUpades
 * @package Contentor\LocalizationApi\Model\Service
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
     * @param $date
     * @throws LocalizedException
     */
    public function execute($date)
    {
        foreach ($this->getUpdatesGateway->execute($date) as $update) {
            $entity = $this->entityResolver->findByContentorId(
                $update['id']
            );
            $handler = $this->handlerList->getHandlerByCode($entity->getContentCode());
            $handler->execute($entity, $update);
        }
    }
}
