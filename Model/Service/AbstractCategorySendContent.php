<?php
namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Api\Data\CategoryInterface as ContentorCategoryInterface;
use Contentor\LocalizationApi\Api\Data\CategoryInterfaceFactory;
use Contentor\LocalizationApi\Api\CategoryRepositoryInterface;
use Contentor\LocalizationApi\Api\StatusRepositoryInterface;
use Contentor\LocalizationApi\Api\TypeRepositoryInterface;
use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Gateway\SendContent;
use Contentor\LocalizationApi\Model\Category\AttributeProviderFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class AbstractCategorySendContent
 * @package Contentor\LocalizationApi\Model\Service
 */
class AbstractCategorySendContent
{
    /**
     * @var string
     */
    protected $_syncType;

    /**
     * @var string
     */
    protected $_syncName;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var AttributeProviderFactory
     */
    protected $attributeProviderFactory;

    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @var ProductRepositoryInterface
     */
    protected $contentorCategoryRepository;

    /**
     * @var SendContent
     */
    protected $sendContent;

    /**
     * @var CategoryInterfaceFactory
     */
    protected $categoryInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var TypeRepositoryInterface
     */
    protected $typeRepository;

    /**
     * @var StatusRepositoryInterface
     */
    protected $statusRepository;
    /**
     * @var TestConnection
     */
    protected $testConnection;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * AbstractCategorySendContent constructor.
     * @param Logger $logger
     * @param AttributeProviderFactory $attributeProviderFactory
     * @param ConfigurationService $configurationService
     * @param CategoryRepositoryInterface $contentorCategoryRepository
     * @param SendContent $sendContent
     * @param CategoryInterfaceFactory $categoryInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DateTime $date
     * @param TypeRepositoryInterface $typeRepository
     * @param StatusRepositoryInterface $statusRepository
     * @param TestConnection $testConnection
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        Logger $logger,
        AttributeProviderFactory $attributeProviderFactory,
        ConfigurationService $configurationService,
        CategoryRepositoryInterface $contentorCategoryRepository,
        SendContent $sendContent,
        CategoryInterfaceFactory $categoryInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DateTime $date,
        TypeRepositoryInterface $typeRepository,
        StatusRepositoryInterface $statusRepository,
        TestConnection $testConnection,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->logger = $logger;
        $this->attributeProviderFactory = $attributeProviderFactory;
        $this->configurationService = $configurationService;
        $this->logger = $logger;
        $this->contentorCategoryRepository = $contentorCategoryRepository;
        $this->sendContent = $sendContent;
        $this->categoryInterfaceFactory = $categoryInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->date = $date;
        $this->typeRepository = $typeRepository;
        $this->statusRepository = $statusRepository;
        $this->testConnection = $testConnection;
        $this->request = $request;
    }


    /**
     * @param CategoryInterface $category
     * @param $sourceLocale
     * @param $targetLocales
     */
    public function execute(CategoryInterface $category, $sourceLocale, $targetLocales)
    {
        $data = $this->getFields($category);
        if (empty($data)) {
            $this->logger->critical(
                sprintf('Empty attributes map. Skip category %s', $category->getId())
            );
            return;
        }

        try {
            $this->logger->info('Sending categoryId: ' . $category->getId() . ' for '. $this->_syncName .' to: ' . implode(' ', array_values($targetLocales)));

            foreach ($targetLocales as $targetId => $targetLocale) {
                $type = 'standard';
                $prevID = false;

                if ($this->configurationService->isVersioningEnabled()) {
                    /*
                     * When versioning is active try to resolve the last update
                     * of this product and locale combination.
                     */
                    $update = $this->contentorCategoryRepository->getLastUpdateByLocale(
                        $category->getId(),
                        $targetLocale,
                        $sourceLocale,
                        $this->_syncType
                    );

                    if (null !== $update->getContentorId()) {
                        $type = 'update';
                        $prevID = $update->getContentorId();
                    }
                }

                $contentorId = $this->sendContent->execute([
                    'sourceLocale' => $sourceLocale,
                    'targetLocale' => $targetLocale,
                    'fields' => $data,
                    'type' => $type,
                    'previous' => $prevID,
                ]);

                if ($contentorId) {
                    $this->saveEntity($contentorId, $category->getId(), $sourceLocale, $targetLocale, $targetId, $type);
                    $this->saveTypeEntity($contentorId);
                    $this->saveStatusEntity(
                        $contentorId,
                        'Sent category for '. $this->_syncName .' to ' . $targetLocale
                    );

                    if ($type == 'update') {
                        $this->logger->info('category: '.$category->getId() . ' to ' . $targetLocale . ' sent as update request to ' . $prevID . ', assigned id ' . $contentorId);
                    } else {
                        $this->logger->info('category: '.$category->getId() . ' to ' . $targetLocale . ' sent as standard request, assigned id ' . $contentorId);
                    }
                } else {
                    $this->logger->critical(
                        sprintf('Api returns empty Contentor ID. Skip categoryId %s', $category->getId())
                    );
                }
            }
        } catch (LocalizedException $localizedException) {
            $this->logger->critical(
                sprintf('Exception for categoryId %s , message : %s',
                    $category->getId(),
                    $localizedException->getMessage()
                )
            );
            throw $localizedException;
        }
    }

    /**
     * @param string $contentorId
     * @param int $categoryId
     * @param string $sourceLocale
     * @param string $targetLocale
     * @param string $targetId
     * @param string $type
     */
    protected function saveEntity($contentorId, $categoryId, $sourceLocale, $targetLocale, $targetId, $type)
    {
        if(!empty($this->request->getParam('machineTranslation'))){
            $machineTranslation = $this->request->getParam('machineTranslation');
        } else {
            $machineTranslation = 'none';
        }
        /** @var ContentorCategoryInterface $category */
        $category = $this->categoryInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $category,
            [
                ContentorCategoryInterface::CONTENTOR_ID => $contentorId,
                ContentorCategoryInterface::CATEGORY_ID => $categoryId,
                ContentorCategoryInterface::SOURCE_LOCALE => $sourceLocale,
                ContentorCategoryInterface::TARGET_LOCALE => $targetLocale,
                ContentorCategoryInterface::TARGET_STORE => $targetId,
                ContentorCategoryInterface::TYPE => $type,
                ContentorCategoryInterface::STATE => 'pending',
                ContentorCategoryInterface::SENT_TIME => $this->date->gmtDate(),
                ContentorCategoryInterface::SYNCHRONIZE_TYPE => $this->_syncType,
                ContentorCategoryInterface::DELIVERY_SPEED => $this->request->getParam('deliverySpeed'),
                ContentorCategoryInterface::MACHINE_TRANSLATION => $machineTranslation
            ],
            ContentorCategoryInterface::class
        );

        $this->contentorCategoryRepository->save($category);
    }

    /**
     * @param string $contentorId
     * @return void
     */
    protected function saveTypeEntity($contentorId)
    {
        $this->typeRepository->saveContentRequest($contentorId,ContentorCategoryInterface::TYPE_CODE);
    }

    /**
     * @param string $contentorId
     * @param string $status
     */
    protected function saveStatusEntity($contentorId, $status)
    {
        $this->statusRepository->saveStatus($contentorId, $status);
    }

    /**
     * @param CategoryInterface $category
     * @return array
     */
    protected function getFields(CategoryInterface $category)
    {
        $attributeProvider = $this->attributeProviderFactory->create([
            'category'    => $category,
            'extraFields' => [],
            'syncType'    => $this->_syncType
        ]);

        return $attributeProvider->getList();
    }
}
