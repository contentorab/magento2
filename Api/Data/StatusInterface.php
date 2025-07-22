<?php

namespace Contentor\LocalizationApi\Api\Data;

/**
 * Interface StatusInterface
 * Data interface for status entities
 * @api
 */
interface StatusInterface
{
    /**
     * Column names
     * @var string
     */
    public const ID = 'id';
    public const CONTENTOR_ID = 'contentor_id';
    public const STATUS_TIME = 'status_time';
    public const STATUS = 'status';

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
     * Set status
     *
     * @param string $status
     * @return void
     */
    public function setStatus($status): void;

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Set status time
     *
     * @param string $statusTime
     * @return void
     */
    public function setStatusTime($statusTime): void;

    /**
     * Get status time
     *
     * @return string
     */
    public function getStatusTime(): string;
}
