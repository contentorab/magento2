<?php
namespace Contentor\LocalizationApi\Api;

use Contentor\LocalizationApi\Api\Data\CategoryInterface;

/**
 * Interface CategoryRepositoryInterface
 * @package Contentor\LocalizationApi\Api
 */
interface CategoryRepositoryInterface
{
    /**
     * @param int $categoryId
     * @param string $targetLocale
     * @param string $sourceLocale
     * @param int $synchronizeType
     * @return CategoryInterface
     */
    public function getLastUpdateByLocale($categoryId , $targetLocale, $sourceLocale, $synchronizeType);

    /**
     * @param CategoryInterface $category
     * @return CategoryInterface
     */
    public function save(CategoryInterface $category);
}
