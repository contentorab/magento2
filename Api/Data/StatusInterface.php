<?php

namespace Contentor\LocalizationApi\Api\Data;

interface StatusInterface
{
    /**
     * Column names
     * @var string
     */
    const ID = 'id';
    const CONTENTOR_ID = 'contentor_id';
    const STATUS_TIME = 'status_time';
    const STATUS = 'status';

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
     * @param string $status
     * @return void
     */
    public function setStatus($status): void;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @param string $statusTime
     * @return void
     */
    public function setStatusTime($statusTime): void;

    /**
     * @return string
     */
    public function getStatusTime(): string;
}
