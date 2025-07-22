<?php

namespace Contentor\LocalizationApi\Api;

use Contentor\LocalizationApi\Api\Data\CategoryInterface;
use Magento\Framework\DataObject;

/**
 * Interface CategoryRepositoryInterface
 * Repository interface for managing category localization data
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * Get last update information for a category by locale
     *
     * @param int $categoryId
     * @param string $targetLocale
     * @param string $sourceLocale
     * @param int $synchronizeType
     * @return DataObject
     */
    public function getLastUpdateByLocale($categoryId, $targetLocale, $sourceLocale, $synchronizeType): DataObject;

    /**
     * Save category localization data
     *
     * @param CategoryInterface $category
     * @return CategoryInterface
     */
    public function save(CategoryInterface $category): CategoryInterface;
}
