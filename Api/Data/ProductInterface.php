<?php

namespace Contentor\LocalizationApi\Api\Data;

/**
 * Interface ProductInterface
 * Data interface for product localization entities
 * @api
 */
interface ProductInterface
{
    /**
     * Column names
     * @var string
     */
    public const CONTENTOR_ID = 'contentor_id';
    public const M2_PRODUCT_ID = 'm2_product_id';
    public const SKU = 'sku';
    public const SOURCE_LOCALE = 'source_locale';
    public const TARGET_LOCALE = 'target_locale';
    public const TARGET_STORE = 'target_store';
    public const SENT_TIME = 'sent_time';
    public const COMPLETED_TIME = 'completed_time';
    public const DEADLINE_TIME = 'deadline_time';
    public const CANCELED_TIME = 'canceled_time';
    public const STATE = 'state';
    public const TYPE = 'type';
    public const TYPE_CODE = 'product';
    public const SYNCHRONIZE_TYPE = 'synchronize_type';
    public const DELIVERY_SPEED = 'delivery_speed';
    public const MACHINE_TRANSLATION = 'machine_translation';
    public const VERSIONING = 'versioning';
    public const ATTRIBUTION = 'attribution';
    public const INTERMEDIATE_VALUE = 'intermediate_value';

    /**
     * Set contentor ID
     *
     * @param int $contentorId
     * @return void
     */
    public function setContentorId($contentorId): void;

    /**
     * Get contentor ID
     *
     * @return int|null
     */
    public function getContentorId();

    /**
     * Set M2 product ID
     *
     * @param int $m2ProductId
     * @return void
     */
    public function setM2ProductId($m2ProductId): void;

    /**
     * Get M2 product ID
     *
     * @return int
     */
    public function getM2ProductId(): int;

    /**
     * Set SKU
     *
     * @param string $sku
     * @return void
     */
    public function setSku($sku): void;

    /**
     * Get SKU
     *
     * @return string
     */
    public function getSku(): string;

    /**
     * Set source locale
     *
     * @param string $sourceLocale
     * @return void
     */
    public function setSourceLocale($sourceLocale): void;

    /**
     * Get source locale
     *
     * @return string
     */
    public function getSourceLocale(): string;

    /**
     * Set target locale
     *
     * @param string $targetLocale
     * @return void
     */
    public function setTargetLocale($targetLocale): void;

    /**
     * Get target locale
     *
     * @return string
     */
    public function getTargetLocale(): string;

    /**
     * Set target store
     *
     * @param string $targetStore
     * @return void
     */
    public function setTargetStore($targetStore): void;

    /**
     * Get target store
     *
     * @return string
     */
    public function getTargetStore(): string;

    /**
     * Set sent time
     *
     * @param string $sendTime
     * @return void
     */
    public function setSentTime($sendTime): void;

    /**
     * Get sent time
     *
     * @return string
     */
    public function getSentTime(): string;

    /**
     * Set completed time
     *
     * @param string $completedTime
     * @return void
     */
    public function setCompletedTime($completedTime): void;

    /**
     * Get completed time
     *
     * @return string
     */
    public function getCompletedTime(): string;

    /**
     * Set deadline time
     *
     * @param string $deadlineTime
     * @return void
     */
    public function setDeadlineTime($deadlineTime): void;

    /**
     * Get deadline time
     *
     * @return string
     */
    public function getDeadlineTime(): string;

    /**
     * Set canceled time
     *
     * @param string $canceledTime
     * @return void
     */
    public function setCanceledTime($canceledTime): void;

    /**
     * Get canceled time
     *
     * @return string
     */
    public function getCanceledTime(): string;

    /**
     * Set state
     *
     * @param string $state
     * @return void
     */
    public function setState($state): void;

    /**
     * Get state
     *
     * @return string
     */
    public function getState(): string;

    /**
     * Set type
     *
     * @param string $type
     * @return void
     */
    public function setType($type): void;

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set synchronize type
     *
     * @param int $type
     * @return void
     */
    public function setSynchronizeType($type): void;

    /**
     * @return int
     */
    public function getSynchronizeType(): int;

    /**
     * Set delivery speed
     *
     * @param string $value
     * @return void
     */
    public function setDeliverySpeed($value): void;

    /**
     * @return string
     */
    public function getDeliverySpeed(): string;

    /**
     * Set machine translation
     *
     * @param string $value
     * @return void
     */
    public function setMachineTranslation($value): void;

    /**
     * @return string
     */
    public function getMachineTranslation(): string;

    /**
     * Set versioning
     *
     * @param string $value
     * @return void
     */
    public function setVersioning($value): void;

    /**
     * Get versioning
     *
     * @return string
     */
    public function getVersioning(): string;

    /**
     * Set attribution
     *
     * @param string $value
     * @return void
     */
    public function setAttribution($value): void;

    /**
     * @return string
     */
    public function getAttribution(): string;

    /**
     * Set intermediate value
     *
     * @param string $value
     * @return void
     */
    public function setIntermediateValue($value): void;

    /**
     * @return string
     */
    public function getIntermediateValue(): string;
}
