<?php
namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\CategoryInterface;
use Contentor\LocalizationApi\Api\Data\CategoryInterfaceFactory;
use Contentor\LocalizationApi\Api\CategoryRepositoryInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Category as CategoryResource;
use Contentor\LocalizationApi\Model\ResourceModel\Category\CollectionFactory;
use Contentor\LocalizationApi\Model\Spi\ContentEntityLoaderInterface;

/**
 * Class CategoryRepository
 * @package Contentor\LocalizationApi\Model
 */
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
        CollectionFactory  $collectionFactory,
        CategoryResource $categoryResource,
        \Contentor\LocalizationApi\Model\CategoryFactory $categoryFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->categoryResource = $categoryResource;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Returns last category content update by locale
     * @param int $categoryId
     * @param string $targetLocale
     * @param string $sourceLocale
     * @param int $synchronizeType
     * @return CategoryInterface|\Magento\Framework\DataObject
     */
    public function getLastUpdateByLocale($categoryId , $targetLocale, $sourceLocale, $synchronizeType)
    {
        /** @var \Contentor\LocalizationApi\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(CategoryInterface::CATEGORY_ID, $categoryId);
        $collection->addFieldToFilter(CategoryInterface::TARGET_LOCALE, $targetLocale);
        $collection->addFieldToFilter(CategoryInterface::SOURCE_LOCALE, $sourceLocale);
        $collection->addFieldToFilter(CategoryInterface::SYNCHRONIZE_TYPE, $synchronizeType);
        $collection->setOrder('sent_time');
        return $collection->getFirstItem();
    }

    /**
     * Save Product Content Request
     *
     * @param CategoryInterface $category
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(CategoryInterface $category)
    {
        $this->categoryResource->save($category);
        return $category;
    }

    /**
     * Returns Category Content Request  by contentor ID
     *
     * @param string $contentorId
     * @return ContentEntityLoaderInterface|CategoryInterface
     */
    public function loadByContentorId($contentorId)
    {
        /** @var Category $model */
        $model =  $this->categoryFactory->create();
        $model->load($contentorId, CategoryInterface::CONTENTOR_ID);
        return $model;
    }
}
