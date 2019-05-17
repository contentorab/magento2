<?php
namespace Contentor\LocalizationApi\Model\Spi;

/**
 * Interface ContentEntityLoaderInterface
 * @package Contentor\LocalizationApi\Model\Spi
 */
interface ContentEntityLoaderInterface
{
    /**
     * @param $contentorId
     * @return ContentEntityInterface
     */
    public function loadByContentorId($contentorId);
}
