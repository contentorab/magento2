<?php
namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\CategoryInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Category as CategoryResource;
use Contentor\LocalizationApi\Model\Spi\ContentEntityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Category
 * @package Contentor\LocalizationApi\Model
 */
class Category extends AbstractModel implements CategoryInterface , ContentEntityInterface
{
    /**#@+
     * Column names
     * @var string
     */
    CONST LOCALIZED_SYNC_TYPE        = 0;

    CONST CONTENT_CREATION_SYNC_TYPE = 1;
    /**#@-*/

    /**
     * @void
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(CategoryResource::class);
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
     * @param int $value
     */
    public function setCategoryId($value)
    {
        $this->setData(
            self::CATEGORY_ID,
            $value
        );
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * @param string $sourceLocale
     * @return void
     */
    public function setSourceLocale($sourceLocale)
    {
        $this->setData(
            self::SOURCE_LOCALE,
            $sourceLocale
        );
    }

    /**
     * @return string
     */
    public function getSourceLocale()
    {
        return $this->getData(self::SOURCE_LOCALE);
    }

    /**
     * @param string $targetLocale
     * @return void
     */
    public function setTargetLocale($targetLocale)
    {
        $this->setData(
            self::TARGET_LOCALE,
            $targetLocale
        );
    }

    /**
     * @return string
     */
    public function getTargetLocale()
    {
        return $this->getData(self::TARGET_STORE);
    }

    /**
     * @param string $targetStore
     * @return void
     */
    public function setTargetStore($targetStore)
    {
        $this->setData(
            self::TARGET_STORE,
            $targetStore
        );
    }

    /**
     * @return string
     */
    public function getTargetStore()
    {
        return $this->getData(self::TARGET_STORE);
    }

    /**
     * @param string $sendTime
     * @return void
     */
    public function setSentTime($sendTime)
    {
        $this->setData(
            self::SENT_TIME,
            $sendTime
        );
    }

    /**
     * @return string
     */
    public function getSentTime()
    {
        return $this->getData(self::SENT_TIME);
    }

    /**
     * @param string $completedTime
     * @return void
     */
    public function setCompletedTime($completedTime)
    {
        $this->setData(
            self::COMPLETED_TIME,
            $completedTime
        );
    }

    /**
     * @return string
     */
    public function getCompletedTime()
    {
        return $this->getData(self::COMPLETED_TIME);
    }

    /**
     * @param string $deadlineTime
     * @return void
     */
    public function setDeadlineTime($deadlineTime)
    {
        $this->setData(
            self::DEADLINE_TIME,
            $deadlineTime
        );
    }

    /**
     * @return string
     */
    public function getDeadlineTime()
    {
        return $this->getData(self::DEADLINE_TIME);
    }

    /**
     * @param string $canceledTime
     * @return void
     */
    public function setCanceledTime($canceledTime)
    {
        $this->setData(
            self::CANCELED_TIME,
            $canceledTime
        );
    }

    /**
     * @return string
     */
    public function getCanceledTime()
    {
        return $this->getData(self::CANCELED_TIME);
    }

    /**
     * @param string $state
     * @return void
     */
    public function setState($state)
    {
        $this->setData(
            self::STATE,
            $state
        );
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->setData(
            self::TYPE,
            $type
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @return string
     */
    public function getContentCode()
    {
        return self::TYPE_CODE;
    }

    /**
     * @return int
     */
    public function getSynchronizeType()
    {
        return $this->getData(self::SYNCHRONIZE_TYPE);
    }

    /**
     * @param int $type
     */
    public function setSynchronizeType($type)
    {
        $this->setData(self::SYNCHRONIZE_TYPE, $type);
    }

    /**
     * @return string
     */
    public function getDeliverySpeed()
    {
        return $this->getData(self::DELIVERY_SPEED);
    }

    /**
     * @param string $value
     */
    public function setDeliverySpeed($value)
    {
        $this->setData(self::DELIVERY_SPEED, $value);
    }

    /**
     * @return string
     */
    public function getMachineTranslation()
    {
        return $this->getData(self::MACHINE_TRANSLATION);
    }

    /**
     * @param string $value
     */
    public function setMachineTranslation($value)
    {
        $this->setData(self::MACHINE_TRANSLATION, $value);
    }

    /**
     * @return string
     */
    public function getAttribution()
    {
        return $this->getData(self::ATTRIBUTION);
    }

    /**
     * @param string $value
     */
    public function setAttribution($value)
    {
        $this->setData(self::ATTRIBUTION, $value);
    }
}
