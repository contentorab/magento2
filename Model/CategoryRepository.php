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
     * @var CategoryInterfaceFactory
     */
    private $categoryInterfaceFactory;

    /**
     * CategoryRepository constructor.
     * @param CollectionFactory $collectionFactory
     * @param CategoryResource $categoryResource
     * @param CategoryInterfaceFactory $categoryInterfaceFactory
     */
    public function __construct(
        CollectionFactory  $collectionFactory,
        CategoryResource $categoryResource,
        CategoryInterfaceFactory $categoryInterfaceFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->categoryResource = $categoryResource;
        $this->categoryInterfaceFactory = $categoryInterfaceFactory;
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
     * Returns Product Content Request  by contentor ID
     *
     * @param int $contentorId
     * @return ContentEntityLoaderInterface|CategoryInterface
     */
    public function loadByContentorId($contentorId)
    {
        /** @var Category $model */
        $model =  $this->categoryInterfaceFactory->create();
        $model->load($contentorId, CategoryInterface::CONTENTOR_ID);
        return $model;
    }
}
