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
 *
 * Product Content update handler.
 * Responsibility : process content updates returned by API.
 * Each Content Entity should have own handler with related to specific
 * Magento entity logic (Product,Category,Cms)
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
        if ($data['state'] == 'completed') {
            $this->handleCompleted($entity, $data);
        } else if($data['state'] == 'confirmed') {
            $this->handleConfirmed($entity, $data);
        } else if($data['state'] == 'canceled') {
            $this->handleCanceled($entity, $data);
        }
    }

    /**
     * Handle the scenario where a request becomes confirmed. This takes
     * care of two scenarios:
     *
     * 1) When it initially receives a deadline
     * 2) If the product goes from completed/canceled to confirmed
     */
    private function handleConfirmed(ContentEntityInterface $entity, array $data) {
        if($entity->getState() == 'confirmed') {
            // This product is already in a confirmed state - skip it
            // TODO: This might need to update the deadline
            return;
        }

        // Update the state and completed time of the product
        $entity->setState($data['state']);
        $this->productRepository->save($entity);

        // Show a status message in the log for the product
        $status = 'Order confirmed for delivery';

        $this->statusRepository->saveStatus(
            $entity->getContentorId(),
            $status
        );
    }

     /**
     * Handle the scenario where a request becomes canceled.
     */
    private function handleCanceled(ContentEntityInterface $entity, array $data) {
        if($entity->getState() == 'canceled') {
            // This product is already in a canceled state - skip it
            return;
        }

        // Update the state and deadline of the product
        $entity->setState($data['state']);
        $this->productRepository->save($entity);

        // Show a status message in the log for the product
        $status = 'Order canceled';

        $this->statusRepository->saveStatus(
            $entity->getContentorId(),
            $status
        );
    }

    /**
     * Handle the scenario where an item is received as completed. The job
     * of this function is to copy back data into the Magento product.
     */
    private function handleCompleted(ContentEntityInterface $entity, array $data) {
        if($entity->getState() == 'completed') {
            // This product is already in a completed state - skip it
            return;
        }

        /** @var \Magento\Catalog\Api\Data\ProductInterface| \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create()->setStoreId(
            $entity->getTargetStore()
        )->loadByAttribute('sku', $entity->getSku());

        if (! $product) {
            // The Magento product represented by this SKU does not exist, abort the update
            return;
        }

        // Copy back the fields from the completed request into the product
        $this->storeManager->setCurrentStore($entity->getTargetStore());
        foreach ($data['fields'] as $field) {
            //Handle localizable field
            if ($field['type'] == 'localizable') {
                $attribute = substr($field['id'], 0, -4);
                $product->setDataUsingMethod($attribute, $field['value']);
            }
            //Handle creatable field
            if ($field['type'] == 'creatable') {
                if ( array_key_exists('value', $field) ) {
                    $attribute = substr($field['id'], 0, -4);
                    $product->setDataUsingMethod($attribute, $field['value']);
                } else {
                    // TODO: Contentor doesn't have value key in testing env
                    return;
                }
            }
        }

        if ($this->configurationService->isAutomationEnabled()) {
            // If the product should go live when received back update its status
            $product->setStatus(1);
        }

        // Save the product with the updated attributes
        $this->catalogProductRepository->save($product);

        // Update the state and completed time of the product
        $entity->setCompletedTime($data['completed']);
        $entity->setState($data['state']);
        $this->productRepository->save($entity);

        // Show a status message in the log for the product
        $status = 'Received as completed for '
            . $data['language']['target']
            . ', completion time: '
            . date("Y-m-d H:i:s", strtotime($data['completed']));

        $this->statusRepository->saveStatus(
            $entity->getContentorId(),
            $status
        );
    }
}
