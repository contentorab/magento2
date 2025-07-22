<?php

namespace Contentor\LocalizationApi\Api\Data;

/**
 * Interface TypeInterface
 * Data interface for type entities
 * @api
 */
interface TypeInterface
{
    /**
     * Column names
     * @var string
     */
    public const CONTENTOR_ID = 'contentor_id';
    public const TYPE = 'type';

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
}
