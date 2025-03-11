<?php

namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\CategoryInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Category as CategoryResource;
use Contentor\LocalizationApi\Model\Spi\ContentEntityInterface;
use Magento\Framework\Model\AbstractModel;

class Category extends AbstractModel implements CategoryInterface, ContentEntityInterface
{
    /**
     * Column names
     * @var string
     */
    const LOCALIZED_SYNC_TYPE = 0;

    const CONTENT_CREATION_SYNC_TYPE = 1;

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(CategoryResource::class);
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
    public function setCategoryId($value): void
    {
        $this->setData(
            self::CATEGORY_ID,
            $value
        );
    }

    /**
     * @inheritdoc
     */
    public function getCategoryId(): int
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSourceLocale($sourceLocale): void
    {
        $this->setData(
            self::SOURCE_LOCALE,
            $sourceLocale
        );
    }

    /**
     * @inheritdoc
     */
    public function getSourceLocale(): string
    {
        return $this->getData(self::SOURCE_LOCALE);
    }

    /**
     * @inheritdoc
     */
    public function setTargetLocale($targetLocale): void
    {
        $this->setData(
            self::TARGET_LOCALE,
            $targetLocale
        );
    }

    /**
     * @inheritdoc
     */
    public function getTargetLocale(): string
    {
        return $this->getData(self::TARGET_STORE);
    }

    /**
     * @inheritdoc
     */
    public function setTargetStore($targetStore): void
    {
        $this->setData(
            self::TARGET_STORE,
            $targetStore
        );
    }

    /**
     * @inheritdoc
     */
    public function getTargetStore(): string
    {
        return $this->getData(self::TARGET_STORE);
    }

    /**
     * @inheritdoc
     */
    public function setSentTime($sendTime): void
    {
        $this->setData(
            self::SENT_TIME,
            $sendTime
        );
    }

    /**
     * @inheritdoc
     */
    public function getSentTime(): string
    {
        return $this->getData(self::SENT_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setCompletedTime($completedTime): void
    {
        $this->setData(
            self::COMPLETED_TIME,
            $completedTime
        );
    }

    /**
     * @inheritdoc
     */
    public function getCompletedTime(): string
    {
        return $this->getData(self::COMPLETED_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setDeadlineTime($deadlineTime): void
    {
        $this->setData(
            self::DEADLINE_TIME,
            $deadlineTime
        );
    }

    /**
     * @inheritdoc
     */
    public function getDeadlineTime(): string
    {
        return $this->getData(self::DEADLINE_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setCanceledTime($canceledTime): void
    {
        $this->setData(
            self::CANCELED_TIME,
            $canceledTime
        );
    }

    /**
     * @inheritdoc
     */
    public function getCanceledTime(): string
    {
        return $this->getData(self::CANCELED_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setState($state): void
    {
        $this->setData(
            self::STATE,
            $state
        );
    }

    /**
     * @inheritdoc
     */
    public function getState(): string
    {
        return $this->getData(self::STATE);
    }

    /**
     * @inheritdoc
     */
    public function setType($type): void
    {
        $this->setData(
            self::TYPE,
            $type
        );
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function getContentCode(): string
    {
        return self::TYPE_CODE;
    }

    /**
     * @inheritdoc
     */
    public function getSynchronizeType(): int
    {
        return $this->getData(self::SYNCHRONIZE_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setSynchronizeType($type): void
    {
        $this->setData(self::SYNCHRONIZE_TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getDeliverySpeed(): string
    {
        return $this->getData(self::DELIVERY_SPEED);
    }

    /**
     * @inheritdoc
     */
    public function setDeliverySpeed($value): void
    {
        $this->setData(self::DELIVERY_SPEED, $value);
    }

    /**
     * @inheritdoc
     */
    public function getMachineTranslation(): string
    {
        return $this->getData(self::MACHINE_TRANSLATION);
    }

    /**
     * @inheritdoc
     */
    public function setMachineTranslation($value): void
    {
        $this->setData(self::MACHINE_TRANSLATION, $value);
    }

    /**
     * @inheritdoc
     */
    public function getAttribution(): string
    {
        return $this->getData(self::ATTRIBUTION);
    }

    /**
     * @inheritdoc
     */
    public function setAttribution($value): void
    {
        $this->setData(self::ATTRIBUTION, $value);
    }

    /**
     * @inheritdoc
     */
    public function getIntermediateValue(): string
    {
        return $this->getData(self::INTERMEDIATE_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setIntermediateValue($value): void
    {
        $this->setData(self::INTERMEDIATE_VALUE, $value);
    }
}
