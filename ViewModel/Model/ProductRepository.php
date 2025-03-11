<?php

namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Contentor\LocalizationApi\Api\Data\ProductInterfaceFactory;
use Contentor\LocalizationApi\Api\ProductRepositoryInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Product as ProductResource;
use Contentor\LocalizationApi\Model\ResourceModel\Product\Collection;
use Contentor\LocalizationApi\Model\ResourceModel\Product\CollectionFactory;
use Contentor\LocalizationApi\Model\Spi\ContentEntityLoaderInterface;
use Magento\Framework\DataObject;

/**
 * Class ProductRepository
 *
 * Main product content request repository
 * implement CRUD for product content request
 */
class ProductRepository implements ProductRepositoryInterface, ContentEntityLoaderInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var ProductInterfaceFactory
     */
    private $productInterfaceFactory;

    /**
     * ProductRepository constructor.
     * @param CollectionFactory $collectionFactory
     * @param ProductResource $productResource
     * @param ProductInterfaceFactory $productInterfaceFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ProductResource $productResource,
        ProductInterfaceFactory $productInterfaceFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->productResource = $productResource;
        $this->productInterfaceFactory = $productInterfaceFactory;
    }

    /**
     * @inheritdoc
     */
    public function getLastUpdateByLocale($sku, $targetLocale, $sourceLocale, $synchronizeType): DataObject
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ProductInterface::SKU, $sku);
        $collection->addFieldToFilter(ProductInterface::TARGET_LOCALE, $targetLocale);
        $collection->addFieldToFilter(ProductInterface::SOURCE_LOCALE, $sourceLocale);
        $collection->addFieldToFilter(ProductInterface::SYNCHRONIZE_TYPE, $synchronizeType);
        $collection->setOrder('sent_time');
        return $collection->getFirstItem();
    }

    /**
     * @inheritdoc
     */
    public function save(ProductInterface $product): ProductInterface
    {
        $this->productResource->save($product);
        return $product;
    }

    /**
     * @inheritdoc
     */
    public function loadByContentorId($contentorId): Product
    {
        /** @var Product $model */
        $model = $this->productInterfaceFactory->create();
        $model->load($contentorId, Product::CONTENTOR_ID);
        return $model;
    }
}
