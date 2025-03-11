<?php

namespace Contentor\LocalizationApi\Api\Data;

interface CategoryInterface
{
    /**
     * Column names
     * @var string
     */
    const CONTENTOR_ID = 'contentor_id';
    const CATEGORY_ID = 'category_id';
    const SOURCE_LOCALE = 'source_locale';
    const TARGET_LOCALE = 'target_locale';
    const TARGET_STORE = 'target_store';
    const SENT_TIME = 'sent_time';
    const COMPLETED_TIME = 'completed_time';
    const DEADLINE_TIME = 'deadline_time';
    const CANCELED_TIME = 'canceled_time';
    const STATE = 'state';
    const TYPE = 'type';
    const TYPE_CODE = 'category';
    const SYNCHRONIZE_TYPE = 'synchronize_type';
    const DELIVERY_SPEED = 'delivery_speed';
    const MACHINE_TRANSLATION = 'machine_translation';
    const ATTRIBUTION = 'attribution';
    const INTERMEDIATE_VALUE = 'intermediate_value';

    /**
     * @param int $contentorId
     * @return void
     */
    public function setContentorId($contentorId): void;

    /**
     * @return mixed
     */
    public function getContentorId();

    /**
     * @param int $value
     * @return void
     */
    public function setCategoryId($value): void;

    /**
     * @return int
     */
    public function getCategoryId(): int;

    /**
     * @param string $sourceLocale
     * @return void
     */
    public function setSourceLocale($sourceLocale): void;

    /**
     * @return string
     */
    public function getSourceLocale(): string;

    /**
     * @param string $targetLocale
     * @return void
     */
    public function setTargetLocale($targetLocale): void;

    /**
     * @return string
     */
    public function getTargetLocale(): string;

    /**
     * @param string $targetStore
     * @return void
     */
    public function setTargetStore($targetStore): void;

    /**
     * @return string
     */
    public function getTargetStore(): string;

    /**
     * @param string $sendTime
     * @return void
     */
    public function setSentTime($sendTime): void;

    /**
     * @return string
     */
    public function getSentTime(): string;

    /**
     * @param string $completedTime
     * @return void
     */
    public function setCompletedTime($completedTime): void;

    /**
     * @return string
     */
    public function getCompletedTime(): string;

    /**
     * @param string $deadlineTime
     * @return void
     */
    public function setDeadlineTime($deadlineTime): void;

    /**
     * @return string
     */
    public function getDeadlineTime(): string;

    /**
     * @param string $canceledTime
     * @return void
     */
    public function setCanceledTime($canceledTime): void;

    /**
     * @return string
     */
    public function getCanceledTime(): string;

    /**
     * @param string $state
     * @return void
     */
    public function setState($state): void;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @param string $type
     * @return void
     */
    public function setType($type): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param int $type
     * @return void
     */
    public function setSynchronizeType($type): void;

    /**
     * @return int
     */
    public function getSynchronizeType(): int;

    /**
     * @param string $value
     * @return void
     */
    public function setDeliverySpeed($value): void;

    /**
     * @return string
     */
    public function getDeliverySpeed(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setMachineTranslation($value): void;

    /**
     * @return string
     */
    public function getMachineTranslation(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setAttribution($value): void;

    /**
     * @return string
     */
    public function getAttribution(): string;

    /**
     * @param string $value
     * @return void
     */
    public function setIntermediateValue($value): void;

    /**
     * @return string
     */
    public function getIntermediateValue(): string;
}
