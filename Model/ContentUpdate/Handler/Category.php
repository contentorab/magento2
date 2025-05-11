<?php

namespace Contentor\LocalizationApi\Model\ContentUpdate\Handler;

use Contentor\LocalizationApi\Api\CategoryRepositoryInterface;
use Contentor\LocalizationApi\Api\StatusRepositoryInterface;
use Contentor\LocalizationApi\Model\Spi\ContentEntityInterface;
use Contentor\LocalizationApi\Model\Spi\ContentUpdateHandlerInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Exception;
use Magento\Catalog\Api\CategoryRepositoryInterface as CatalogCategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Category
 *
 * Category Content update handler.
 * Responsibility : process content updates returned by API.
 * Each Content Entity should have own handler with related to specific
 * Magento entity logic (Product,Category,Cms)
 */
class Category extends AbstractHandler implements ContentUpdateHandlerInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CatalogCategoryRepositoryInterface
     */
    private $catalogCategoryRepository;

    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var StatusRepositoryInterface
     */
    private $statusRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Category constructor.
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param CatalogCategoryRepositoryInterface $catalogCategoryRepository
     * @param ConfigurationService $configurationService
     * @param StatusRepositoryInterface $statusRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        CatalogCategoryRepositoryInterface $catalogCategoryRepository,
        ConfigurationService $configurationService,
        StatusRepositoryInterface $statusRepository,
        LoggerInterface $logger
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->catalogCategoryRepository = $catalogCategoryRepository;
        $this->configurationService = $configurationService;
        $this->statusRepository = $statusRepository;
        $this->logger = $logger;
    }

    /**
     * @param ContentEntityInterface $entity
     * @param array $data
     */
    public function execute(ContentEntityInterface $entity, array $data): void
    {
        try {
            switch ($data['state']) {
                case 'completed':
                    $this->handleCompleted($entity, $data);
                    break;
                case 'confirmed':
                    $this->handleConfirmed($entity, $data);
                    break;
                case 'canceled':
                    $this->handleCanceled($entity, $data);
                    break;
                case 'pending':
                    $this->handlePending($entity, $data);
                    break;
                default:
                    break;
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Handle the scenario where an item is received as completed. The job
     * of this function is to copy back data into the Magento product.
     *
     * @param ContentEntityInterface $entity
     * @param array $data
     */
    private function handleCompleted(ContentEntityInterface $entity, array $data): void
    {
        try {
            if ($entity->getState() == 'completed') {
                // This product is already in a completed state - skip it
                return;
            }

            if (!$entity->getCategoryId()) {
                return;
            }
            /** @var CategoryInterface $category */
            $category = $this->loadCategory($entity, $this->getAttributeCodes($data['fields']));
            if ($category) {

                // Copy back the fields from the completed request into the product
                $this->storeManager->setCurrentStore($entity->getTargetStore());

                foreach ($data['fields'] as $field) {
                    //Handle localizable field
                    if ($field['type'] == 'localizable') {
                        $attribute = $this->getAttributeCode($field['id']);
                        $category->setData($attribute, $field['value']);
                    }
                    //Handle creatable field
                    if ($field['type'] == 'creatable') {
                        if (array_key_exists('value', $field)) {
                            $attribute = $this->getAttributeCode($field['id']);
                            $category->setData($attribute, $field['value']);
                        } else {
                            // TODO: Contentor doesn't have value key in testing env
                            return;
                        }
                    }
                }

                if ($this->configurationService->isCategoryAutomationEnabled()) {
                    // If the category should go live when received back update its status
                    $category->setIsActive(1);
                }

                // Save the product with the updated attributes
                $this->catalogCategoryRepository->save($category);

                // Update the state and completed time of the product
                $entity->setCompletedTime($data['completed']);
                $entity->setState($data['state']);

                $entity->setAttribution(
                    $entity->getMachineTranslation() === 'only-automatic'
                        ? 'google-translate'
                        : 'none'
                );
                $this->categoryRepository->save($entity);

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
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Handle the scenario where a request becomes confirmed. This takes
     * care of two scenarios:
     * 1) When it initially receives a deadline
     * 2) If the product goes from completed/canceled to confirmed
     * @param ContentEntityInterface $entity
     * @param array $data
     */
    private function handleConfirmed(ContentEntityInterface $entity, array $data)
    {
        try {
            if ($entity->getState() == 'confirmed') {
                // This product is already in a confirmed state - skip it
                return;
            }

            // Update the state and completed time of the product
            $entity->setState($data['state']);
            $this->categoryRepository->save($entity);

            // Show a status message in the log for the product
            $status = 'Order confirmed for delivery';

            $this->statusRepository->saveStatus(
                $entity->getContentorId(),
                $status
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Handle the scenario where a request becomes canceled.
     * @param ContentEntityInterface $entity
     * @param array $data
     */
    private function handleCanceled(ContentEntityInterface $entity, array $data)
    {
        if ($entity->getState() == 'canceled') {
            // This product is already in a canceled state - skip it
            return;
        }

        // Update the state and deadline of the product
        $entity->setState($data['state']);
        $this->categoryRepository->save($entity);

        // Show a status message in the log for the product
        $status = 'Order canceled';

        $this->statusRepository->saveStatus(
            $entity->getContentorId(),
            $status
        );
    }

    /**
     * Handles entries that are pending, currently only handle entries with intermediate_value
     * @param ContentEntityInterface $entity
     * @param $data
     */
    private function handlePending(ContentEntityInterface $entity, $data): void
    {

        /** @var CategoryInterface $category */
        $category = $this->categoryCollectionFactory->create()->setStoreId(
            $entity->getTargetStore()
        )->load($entity->getCategoryId());

        // Copy the intermediate_value from the request to the product, if any product has been updated.
        $updated = false;
        $this->storeManager->setCurrentStore($entity->getTargetStore());
        foreach ($data['fields'] as $field) {
            if ($field['type'] == 'localizable') {
                if (array_key_exists('intermediateValue', $field)) {
                    $attribute = $this->getAttributeCode($field['id']);
                    $category->setData($attribute, $field['intermediateValue']);
                    $updated = true;
                }
            }
        }

        // If no fields where updated, skip saving and updating.
        if (!$updated) {
            return;
        }

        if ($this->configurationService->isAutomationEnabled()) {
            // If the product should go live when received back update its status
            $category->setStatus(1);
        }

        // Save the product with the updated attributes
        $this->catalogCategoryRepository->save($category);

        // Update the state and completed time of the product
        $entity->setState($data['state']);
        $entity->setAttribution('google-translate');
        $this->categoryRepository->save($entity);

        $status = 'Received intermediateValue';

        $this->statusRepository->saveStatus(
            $entity->getContentorId(),
            $status
        );
    }

    protected function loadCategory(ContentEntityInterface $entity, array $attributeCodes)
    {
        $collection = $this->categoryCollectionFactory->create()
            ->setStoreId($entity->getTargetStore())
            ->addAttributeToSelect($attributeCodes)
            ->addAttributeToFilter('entity_id', $entity->getCategoryId());
        $category = $collection->getFirstItem();
        return $category;
    }
}
