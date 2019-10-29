<?php
namespace Contentor\LocalizationApi\Api;

/**
 * Interface StatusRepositoryInterface
 * @package Contentor\LocalizationApi\Api
 */
interface StatusRepositoryInterface
{
    /**
     * Save product contentor status.
     * Should be used for status updates, to avoid direct DV queries
     *
     * @param int $contentorId
     * @param string $status
     * @param null $date
     * @return \Contentor\LocalizationApi\Api\Data\StatusInterface
     */
    public function saveStatus($contentorId, $status, $date = null);
}
