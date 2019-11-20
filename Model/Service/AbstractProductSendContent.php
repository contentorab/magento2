<?php
namespace Contentor\LocalizationApi\Model\Service;

use Contentor\LocalizationApi\Api\Data\ProductInterface as ContentorProductInterface;
use Contentor\LocalizationApi\Api\Data\ProductInterfaceFactory;
use Contentor\LocalizationApi\Api\ProductRepositoryInterface;
use Contentor\LocalizationApi\Api\StatusRepositoryInterface;
use Contentor\LocalizationApi\Api\TypeRepositoryInterface;
use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Gateway\SendContent;
use Contentor\LocalizationApi\Model\Product\AttributeProviderFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class ProductSendContent
 * @package Contentor\LocalizationApi\Model\Service
 * Abstract class for localizable and creatable features
 */
class AbstractProductSendContent
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
    protected $contentorProductRepository;

    /**
     * @var SendContent
     */
    protected $sendContent;

    /**
     * @var ProductInterfaceFactory
     */
    protected $productInterfaceFactory;

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
     * AbstractProductSendContent constructor.
     * @param Logger $logger
     * @param AttributeProviderFactory $attributeProviderFactory
     * @param ConfigurationService $configurationService
     * @param ProductRepositoryInterface $contentorProductRepository
     * @param SendContent $sendContent
     * @param ProductInterfaceFactory $productInterfaceFactory
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
        ProductRepositoryInterface $contentorProductRepository,
        SendContent $sendContent,
        ProductInterfaceFactory $productInterfaceFactory,
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
        $this->contentorProductRepository = $contentorProductRepository;
        $this->sendContent = $sendContent;
        $this->productInterfaceFactory = $productInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->date = $date;
        $this->typeRepository = $typeRepository;
        $this->statusRepository = $statusRepository;
        $this->testConnection = $testConnection;
        $this->request = $request;
    }


    /**
     * Send content request to contentor.
     *
     * Normalize magento product attributes, search localizable/created  attributes and prepare data for API
     *
     * @param ProductInterface $product
     * @param string $sourceLocale
     * @param array $targetLocales
     * @return void
     */
    public function execute(ProductInterface $product, $sourceLocale,  $targetLocales)
    {
        $data = $this->getFields($product);
        if (empty($data)) {
            $this->logger->critical(
                sprintf('Empty attributes map. Skip product %s', $product->getSku())
            );
            return;
        }

        try {
            $this->logger->info('Sending ' . $product->getSku() . ' for '. $this->_syncName .' to: ' . implode(' ', array_values($targetLocales)));

            foreach ($targetLocales as $targetId => $targetLocale) {
                $type = 'standard';
                $prevID = false;

                if ($this->configurationService->isVersioningEnabled()) {
                    /*
                     * When versioning is active try to resolve the last update
                     * of this product and locale combination.
                     */
                    $update = $this->contentorProductRepository->getLastUpdateByLocale(
                        $product->getSku(),
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
                    $this->saveEntity($contentorId, $product->getSku(), $sourceLocale, $targetLocale, $targetId, $type);
                    $this->saveTypeEntity($contentorId);
                    $this->saveStatusEntity(
                        $contentorId,
                        'Sent for '. $this->_syncName .' to ' . $targetLocale
                    );

                    if ($type == 'update') {
                        $this->logger->info($product->getSku() . ' to ' . $targetLocale . ' sent as update request to ' . $prevID . ', assigned id ' . $contentorId);
                    } else {
                        $this->logger->info($product->getSku() . ' to ' . $targetLocale . ' sent as standard request, assigned id ' . $contentorId);
                    }
                } else {
                    $this->logger->critical(
                        sprintf('Api returns empty Contentor ID. Skip product %s', $product->getSku())
                    );
                }
            }
        } catch (LocalizedException $localizedException) {
            $this->logger->critical(
                sprintf('Exception for product %s , message : %s',
                    $product->getSku(),
                    $localizedException->getMessage()
                )
            );
        }
    }

    /**
     * @param int $contentorId
     * @param string $sku
     * @param string $sourceLocale
     * @param string $targetLocale
     * @param string $targetId
     * @param string $type
     */
    protected function saveEntity($contentorId, $sku, $sourceLocale, $targetLocale, $targetId, $type)
    {
        /** @var ContentorProductInterface $product */
        $product = $this->productInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $product,
            [
                ContentorProductInterface::CONTENTOR_ID => $contentorId,
                ContentorProductInterface::SKU => $sku,
                ContentorProductInterface::SOURCE_LOCALE => $sourceLocale,
                ContentorProductInterface::TARGET_LOCALE => $targetLocale,
                ContentorProductInterface::TARGET_STORE => $targetId,
                ContentorProductInterface::TYPE => $type,
                ContentorProductInterface::STATE => 'pending',
                ContentorProductInterface::SENT_TIME => $this->date->gmtDate(),
                ContentorProductInterface::SYNCHRONIZE_TYPE => $this->_syncType,
                ContentorProductInterface::DELIVERY_SPEED => $this->request->getParam('deliverySpeed')
            ],
            ContentorProductInterface::class
        );

        $this->contentorProductRepository->save($product);
    }

    /**
     * @param int $contentorId
     * @return void
     */
    protected function saveTypeEntity($contentorId)
    {
        $this->typeRepository->saveContentRequest($contentorId);
    }

    /**
     * @param int $contentorId
     * @param string $status
     */
    protected function saveStatusEntity($contentorId, $status)
    {
        $this->statusRepository->saveStatus($contentorId, $status);
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    protected function getFields(ProductInterface $product)
    {
        $attributeProvider = $this->attributeProviderFactory->create([
            'product' => $product,
            'extraFields' => [],
            'syncType' => $this->_syncType
        ]);

        return $attributeProvider->getList();
    }
}
