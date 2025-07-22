<?php

namespace Contentor\LocalizationApi\Api;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Magento\Framework\DataObject;

/**
 * Interface ProductRepositoryInterface
 * Repository interface for managing product localization data
 * @api
 */
interface ProductRepositoryInterface
{
    /**
     * Get last update information for a product by locale
     *
     * @param string $sku
     * @param string $targetLocale
     * @param string $sourceLocale
     * @param int $synchronizeType
     * @return DataObject
     */
    public function getLastUpdateByLocale($sku, $targetLocale, $sourceLocale, $synchronizeType): DataObject;

    /**
     * Save product localization data
     *
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function save(ProductInterface $product): ProductInterface;
}
