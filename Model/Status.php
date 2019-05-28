<?php
namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\StatusInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Status as StatusResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Status
 * @package Contentor\LocalizationApi\Model
 * Status model. Represent data from `contentor_status` table
 */
class Status extends AbstractModel implements StatusInterface
{
    /**
     * @void
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(StatusResource::class);
    }

    /**
     * @param  int $contentorId
     * @return void
     */
    public function setContentorId($contentorId)
    {
        $this->setData(
            self::CONTENTOR_ID,
            $contentorId
        );
    }

    /**
     * @return int
     */
    public function getContentorId()
    {
        return $this->getData(self::CONTENTOR_ID);
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->setData(
            self::STATUS,
            $status
        );
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string $statusTime
     * @return void
     */
    public function setStatusTime($statusTime)
    {
        $this->setData(
            self::STATUS_TIME,
            $statusTime
        );
    }

    /**
     * @return string
     */
    public function getStatusTime()
    {
        return $this->getData(self::STATUS_TIME);
    }
}
