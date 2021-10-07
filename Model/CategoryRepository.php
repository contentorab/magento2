<?php

namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\CategoryRepositoryInterface;
use Contentor\LocalizationApi\Api\Data\CategoryInterface;
use Contentor\LocalizationApi\Api\Data\CategoryInterfaceFactory;
use Contentor\LocalizationApi\Model\ResourceModel\Category as CategoryResource;
use Contentor\LocalizationApi\Model\ResourceModel\Category\Collection;
use Contentor\LocalizationApi\Model\ResourceModel\Category\CollectionFactory;
use Contentor\LocalizationApi\Model\Spi\ContentEntityInterface;
use Contentor\LocalizationApi\Model\Spi\ContentEntityLoaderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;

class CategoryRepository implements CategoryRepositoryInterface, ContentEntityLoaderInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CategoryResource
     */
    private $categoryResource;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        CategoryResource $categoryResource,
        CategoryFactory $categoryFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->categoryResource = $categoryResource;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @inheritdoc
     */
    public function getLastUpdateByLocale($categoryId, $targetLocale, $sourceLocale, $synchronizeType): DataObject
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(CategoryInterface::CATEGORY_ID, $categoryId);
        $collection->addFieldToFilter(CategoryInterface::TARGET_LOCALE, $targetLocale);
        $collection->addFieldToFilter(CategoryInterface::SOURCE_LOCALE, $sourceLocale);
        $collection->addFieldToFilter(CategoryInterface::SYNCHRONIZE_TYPE, $synchronizeType);
        $collection->setOrder('sent_time');
        return $collection->getFirstItem();
    }

    /**
     * @inheritdoc
     */
    public function save(CategoryInterface $category): CategoryInterface
    {
        $this->categoryResource->save($category);
        return $category;
    }

    /**
     * @inheritdoc
     */
    public function loadByContentorId($contentorId)
    {
        /** @var Category $model */
        $model = $this->categoryFactory->create();
        $model->load($contentorId, CategoryInterface::CONTENTOR_ID);
        return $model;
    }
}
