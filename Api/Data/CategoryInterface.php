<?php
namespace Contentor\LocalizationApi\Api\Data;

interface CategoryInterface
{
    /**#@+
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
    /**#@-*/

    /**
     * @param  int $contentorId
     * @return void
     */
    public function setContentorId($contentorId);

    /**
     * @return int
     */
    public function getContentorId();


    /**
     * @param int $value
     * @return void
     */
    public function setCategoryId($value);

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param string $sourceLocale
     * @return void
     */
    public function setSourceLocale($sourceLocale);

    /**
     * @return string
     */
    public function getSourceLocale();

    /**
     * @param string $targetLocale
     * @return void
     */
    public function setTargetLocale($targetLocale);

    /**
     * @return string
     */
    public function getTargetLocale();

    /**
     * @param string $targetStore
     * @return void
     */
    public function setTargetStore($targetStore);

    /**
     * @return string
     */
    public function getTargetStore();

    /**
     * @param string $sendTime
     * @return void
     */
    public function setSentTime($sendTime);

    /**
     * @return string
     */
    public function getSentTime();

    /**
     * @param string $completedTime
     * @return void
     */
    public function setCompletedTime($completedTime);

    /**
     * @return string
     */
    public function getCompletedTime();

    /**
     * @param string $deadlineTime
     * @return void
     */
    public function setDeadlineTime($deadlineTime);

    /**
     * @return string
     */
    public function getDeadlineTime();

    /**
     * @param string $canceledTime
     * @return void
     */
    public function setCanceledTime($canceledTime);

    /**
     * @return string
     */
    public function getCanceledTime();

    /**
     * @param string $state
     * @return void
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $type
     * @return void
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param int $type
     * @return boolean
     */
    public function setSynchronizeType($type);

    /**
     * @return int
     */
    public function getSynchronizeType();

    /**
     * @param string $value
     * @return boolean
     */
    public function setDeliverySpeed($value);

    /**
     * @return string
     */
    public function getDeliverySpeed();
}
