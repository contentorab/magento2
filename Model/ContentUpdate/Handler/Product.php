<?php
namespace Contentor\LocalizationApi\Model\ContentUpdate\Handler;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Contentor\LocalizationApi\Api\ProductRepositoryInterface;
use Contentor\LocalizationApi\Api\StatusRepositoryInterface;
use Contentor\LocalizationApi\Model\Spi\ContentEntityInterface;
use Contentor\LocalizationApi\Model\Spi\ContentUpdateHandlerInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Catalog\Api\ProductRepositoryInterface as CatalogProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Product
 * @package Contentor\LocalizationApi\Model\ContentUpdate\Handler
 */
class Product implements ContentUpdateHandlerInterface
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CatalogProductRepositoryInterface
     */
    private $catalogProductRepository;

    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var StatusRepositoryInterface
     */
    private $statusRepository;

    /**
     * Product constructor.
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param CatalogProductRepositoryInterface $catalogProductRepository
     * @param ConfigurationService $configurationService
     * @param StatusRepositoryInterface $statusRepository
     */
    public function __construct(
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        CatalogProductRepositoryInterface $catalogProductRepository,
        ConfigurationService $configurationService,
        StatusRepositoryInterface $statusRepository
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->catalogProductRepository = $catalogProductRepository;
        $this->configurationService = $configurationService;
        $this->statusRepository = $statusRepository;
    }

    /**
     * @param ContentEntityInterface|ProductInterface $entity
     * @param array $data
     * @return void
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws StateException
     */
    public function execute(ContentEntityInterface $entity, array $data)
    {
        if ($data['state'] != 'completed') {
            return;
        }
        /** @var \Magento\Catalog\Api\Data\ProductInterface| \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create()->setStoreId(
            $entity->getTargetStore()
        )->loadByAttribute('sku', $entity->getSku());

        if (!$product) {
            return;
        }
        // setData on product depending on licalizationsfields received on the right store
        $this->storeManager->setCurrentStore($entity->getTargetStore());
        foreach ($data['fields'] as $field) {
            if ($field['type'] == 'localizable') {
                $attribute = substr($field['id'], 0, -4);
                $product->setDataUsingMethod($attribute, $field['value']);
            }
        }

        if ($this->configurationService->isAutomationEnabled()) {
            $product->setStatus(1);
        }

        //1. save product
        $this->catalogProductRepository->save($product);
        //2. update contentor
        $entity->setCompletedTime($data['completed_time']);
        $this->productRepository->save($entity);
        //3. Update Status
        $status = 'Received as completed for '
            . $data['language']['target']
            . ', completion time: '
            . date("Y-m-d H:i:s", strtotime($data['completed']));

        $this->statusRepository->saveProductStatus(
            $entity->getContentorId(),
            $status
        );
    }
}