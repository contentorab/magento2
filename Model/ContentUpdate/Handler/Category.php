<?php
namespace Contentor\LocalizationApi\Model\ContentUpdate\Handler;

use Contentor\LocalizationApi\Api\CategoryRepositoryInterface;
use Contentor\LocalizationApi\Api\StatusRepositoryInterface;
use Contentor\LocalizationApi\Model\Spi\ContentEntityInterface;
use Contentor\LocalizationApi\Model\Spi\ContentUpdateHandlerInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Catalog\Api\CategoryRepositoryInterface as CatalogCategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 *
 * Category Content update handler.
 * Responsibility : process content updates returned by API.
 * Each Content Entity should have own handler with related to specific
 * Magento entity logic (Product,Category,Cms)
 * Class Category
 * @package Contentor\LocalizationApi\Model\ContentUpdate\Handler
 */
class Category implements ContentUpdateHandlerInterface
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

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
     * Category constructor.
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param CatalogCategoryRepositoryInterface $catalogCategoryRepository
     * @param ConfigurationService $configurationService
     * @param StatusRepositoryInterface $statusRepository
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        CatalogCategoryRepositoryInterface $catalogCategoryRepository,
        ConfigurationService $configurationService,
        StatusRepositoryInterface $statusRepository
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->catalogCategoryRepository = $catalogCategoryRepository;
        $this->configurationService = $configurationService;
        $this->statusRepository = $statusRepository;
    }

    /**
     * @param ContentEntityInterface $entity
     * @param array $data
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
        $this->categoryRepository->save($entity);

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
        $this->categoryRepository->save($entity);

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

        if ( !$entity->getCategoryId() ) {
            return;
        }

        /** @var \Magento\Catalog\Api\Data\CategoryInterface $category */
        $category = $this->categoryFactory->create()->setStoreId(
            $entity->getTargetStore()
        )->load($entity->getCategoryId());

        if ( !$category ) {
            // The Magento product represented by this CategoryId does not exist, abort the update
            return;
        }

        // Copy back the fields from the completed request into the product
        $this->storeManager->setCurrentStore($entity->getTargetStore());

        foreach ($data['fields'] as $field) {
            //Handle localizable field
            if ($field['type'] == 'localizable') {
                $attribute = substr($field['id'], 0, -4);
                $category->setDataUsingMethod($attribute, $field['value']);
            }
            //Handle creatable field
            if ($field['type'] == 'creatable') {
                if ( array_key_exists('value', $field) ) {
                    $attribute = substr($field['id'], 0, -4);
                    $category->setDataUsingMethod($attribute, $field['value']);
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

        $entity->setAttribution($entity->getMachineTranslation() === 'only-automatic' ? 'google-translate' : 'none');
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
}
