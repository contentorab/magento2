<?php
namespace Contentor\LocalizationApi\Model\Spi;

/**
 * Interface ContentEntityInterface
 * @package Contentor\LocalizationApi\Model\Spi
 *
 * Identify that entity supported in contentor API
 * @see  \Contentor\LocalizationApi\Model\Product
 */
interface ContentEntityInterface
{
    /**
     * Returns content entity code
     *
     * @return string
     */
    public function getContentCode();
}
