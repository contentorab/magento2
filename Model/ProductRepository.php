<?php
namespace Contentor\LocalizationApi\Model;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Contentor\LocalizationApi\Api\Data\ProductInterfaceFactory;
use Contentor\LocalizationApi\Api\ProductRepositoryInterface;
use Contentor\LocalizationApi\Model\ResourceModel\Product as ProductResource;
use Contentor\LocalizationApi\Model\ResourceModel\Product\CollectionFactory;
use Contentor\LocalizationApi\Model\Spi\ContentEntityLoaderInterface;

/**
 * Class ProductRepository
 * @package Contentor\LocalizationApi\Model
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
     */
    public function __construct(
        CollectionFactory  $collectionFactory,
        ProductResource $productResource,
        ProductInterfaceFactory $productInterfaceFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->productResource = $productResource;
        $this->productInterfaceFactory = $productInterfaceFactory;
    }

    /**
     * @param string $sku
     * @param string $targetLocale
     * @param string $sourceLocale
     * @return ProductInterface|\Magento\Framework\DataObject
     */
    public function getLastUpdateByLocale($sku , $targetLocale, $sourceLocale)
    {
        /** @var \Contentor\LocalizationApi\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ProductInterface::SKU, $sku);
        $collection->addFieldToFilter(ProductInterface::TARGET_LOCALE, $targetLocale);
        $collection->addFieldToFilter(ProductInterface::SOURCE_LOCALE, $sourceLocale);
        $collection->setOrder('sent_time');
        return $collection->getFirstItem();
    }

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(ProductInterface $product)
    {
        $this->productResource->save($product);
        return $product;
    }

    /**
     * @param int $contentorId
     * @return ContentEntityLoaderInterface|ProductInterface
     */
    public function loadByContentorId($contentorId)
    {
        /** @var Product $model */
        $model =  $this->productInterfaceFactory->create();
        $model->load($contentorId, Product::CONTENTOR_ID);
        return $model;
    }
}
