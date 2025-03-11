<?php

namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\StatusInterface;
use Contentor\LocalizationApi\Api\Data\StatusInterfaceFactory;
use Contentor\LocalizationApi\Api\StatusRepositoryInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Status;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class StatusRepository
 *
 * @api
 * Allow to work with contentor status relations.
 */
class StatusRepository implements StatusRepositoryInterface
{
    /**
     * @var StatusInterfaceFactory
     */
    private $statusInterfaceFactory;

    /**
     * @var ResourceModel\Status
     */
    private $statusResource;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * StatusRepository constructor.
     * @param StatusInterfaceFactory $statusInterfaceFactory
     * @param ResourceModel\Status $statusResource
     * @param DateTime $dateTime
     */
    public function __construct(
        StatusInterfaceFactory $statusInterfaceFactory,
        Status $statusResource,
        DateTime $dateTime
    ) {
        $this->statusInterfaceFactory = $statusInterfaceFactory;
        $this->statusResource = $statusResource;
        $this->dateTime = $dateTime;
    }

    /**
     * @inheritdoc
     */
    public function saveStatus($contentorId, $status, $date = null): StatusInterface
    {
        if (null === $date) {
            $date = $this->dateTime->gmtDate();
        }
        $dto = $this->statusInterfaceFactory->create();
        $dto->setContentorId($contentorId);
        $dto->setStatus($status);
        $dto->setStatusTime($date);

        $this->statusResource->save($dto);

        return $dto;
    }
}
