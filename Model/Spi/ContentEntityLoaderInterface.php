<?php

namespace Contentor\LocalizationApi\Model\Spi;

/**
 * Interface ContentEntityLoaderInterface
 *
 * Represent entity loader repository
 * @see \Contentor\LocalizationApi\Model\EntityResolver
 */
interface ContentEntityLoaderInterface
{
    /**
     * Returns Product Content Request by contentor ID
     *
     * @param int|string $contentorId
     * @return mixed
     */
    public function loadByContentorId($contentorId);
}
