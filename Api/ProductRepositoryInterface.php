<?php

namespace Contentor\LocalizationApi\Api;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Magento\Framework\DataObject;

interface ProductRepositoryInterface
{
    /**
     * @param string $sku
     * @param string $targetLocale
     * @param string $sourceLocale
     * @param int $synchronizeType
     * @return DataObject
     */
    public function getLastUpdateByLocale($sku, $targetLocale, $sourceLocale, $synchronizeType): DataObject;

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function save(ProductInterface $product): ProductInterface;
}
