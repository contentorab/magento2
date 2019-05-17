<?php
namespace Contentor\LocalizationApi\Api;

use Contentor\LocalizationApi\Api\Data\ProductInterface;

/**
 * Interface ProductRepositoryInterface
 * @package Contentor\LocalizationApi\Api
 */
interface ProductRepositoryInterface
{
    /**
     * @param string $sku
     * @param string $targetLocale
     * @param string $sourceLocale
     * @return ProductInterface
     */
    public function getLastUpdateByLocale($sku , $targetLocale, $sourceLocale);

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function save(ProductInterface $product);
}