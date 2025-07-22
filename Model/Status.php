<?php

namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\StatusInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Status as StatusResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Status
 *
 * Status model. Represent data from `contentor_status` table
 */
class Status extends AbstractModel implements StatusInterface
{
    /**
     * Initialize model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(StatusResource::class);
    }

    /**
     * @inheritdoc
     */
    public function setContentorId($contentorId): void
    {
        $this->setData(
            self::CONTENTOR_ID,
            $contentorId
        );
    }

    /**
     * @inheritdoc
     */
    public function getContentorId()
    {
        return $this->getData(self::CONTENTOR_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status): void
    {
        $this->setData(
            self::STATUS,
            $status
        );
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatusTime($statusTime): void
    {
        $this->setData(
            self::STATUS_TIME,
            $statusTime
        );
    }

    /**
     * @inheritdoc
     */
    public function getStatusTime(): string
    {
        return $this->getData(self::STATUS_TIME);
    }
}
