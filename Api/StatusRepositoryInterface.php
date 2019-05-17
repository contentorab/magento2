<?php
namespace Contentor\LocalizationApi\Api;

/**
 * Interface StatusRepositoryInterface
 * @package Contentor\LocalizationApi\Api
 */
interface StatusRepositoryInterface
{
    /**
     * @param int $contentorId
     * @param string $status
     * @param null $date
     * @return \Contentor\LocalizationApi\Api\Data\StatusInterface
     */
    public function saveProductStatus($contentorId, $status, $date = null);
}