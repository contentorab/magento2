<?php

namespace Contentor\LocalizationApi\Api;

use Contentor\LocalizationApi\Api\Data\StatusInterface;

interface StatusRepositoryInterface
{
    /**
     * Save product contentor status.
     * Should be used for status updates, to avoid direct DV queries
     *
     * @param int $contentorId
     * @param string $status
     * @param null $date
     * @return StatusInterface
     */
    public function saveStatus($contentorId, $status, $date = null): StatusInterface;
}
