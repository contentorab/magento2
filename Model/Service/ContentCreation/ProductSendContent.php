<?php
namespace Contentor\LocalizationApi\Model\Service\ContentCreation;

use Contentor\LocalizationApi\Api\Data\ProductInterface as ContentorProductInterface;
use Contentor\LocalizationApi\Api\Data\ProductInterfaceFactory;
use Contentor\LocalizationApi\Api\ProductRepositoryInterface;
use Contentor\LocalizationApi\Api\StatusRepositoryInterface;
use Contentor\LocalizationApi\Api\TypeRepositoryInterface;
use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Gateway\SendContent;
use \Contentor\LocalizationApi\Model\Product\ContentCreation\AttributeProviderFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class ProductSendContent
 * @package Contentor\LocalizationApi\Model\Service\ContentCreation
 */
class ProductSendContent
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var AttributeProviderFactory
     */
    private $attributeProviderFactory;

    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var ProductRepositoryInterface
     */
    private $contentorProductRepository;

    /**
     * @var SendContent
     */
    private $sendContent;

    /**
     * @var ProductInterfaceFactory
     */
    private $productInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var TypeRepositoryInterface
     */
    private $typeRepository;

    /**
     * @var StatusRepositoryInterface
     */
    private $statusRepository;
    /**
     * @var TestConnection
     */
    private $testConnection;

    /**
     * ProductSendContent constructor.
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
        \Contentor\LocalizationApi\Model\Service\TestConnection $testConnection
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
    }


    /**
     * Send content request to contentor.
     *
     * Normalize magento product attributes, search localizeble  attirbutes and prepare data for API
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

            $contentorId = null;

            $this->logger->info('Sending ' . $product->getSku() . ' for content creation to: ' . implode(' ', array_values($targetLocales)));

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
                        \Contentor\LocalizationApi\Model\Product::CONTENT_CREATION_SYNC_TYPE
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
                        'Sent for content creation to ' . $targetLocale
                    );

                    if ($type == 'update') {
                        $this->logger->info($product->getSku() . ' to ' . $targetLocale . ' sent as update request ( content creation ) to ' . $prevID . ', assigned id ' . $contentorId);
                    } else {
                        $this->logger->info($product->getSku() . ' to ' . $targetLocale . ' sent as standard request ( content creation ) , assigned id ' . $contentorId);
                    }
                } else {
                    $this->logger->critical(
                        sprintf('Api returns empty Contentor ID. Skip product %s', $product->getSku())
                    );
                }
            }
        } catch (LocalizedException $localizedException) {
            $this->logger->critical(
                sprintf('Exception for product %s , message :',
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
    private function saveEntity($contentorId, $sku, $sourceLocale, $targetLocale, $targetId, $type)
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
                ContentorProductInterface::SYNCHRONIZE_TYPE => \Contentor\LocalizationApi\Model\Product::CONTENT_CREATION_SYNC_TYPE,
            ],
            ContentorProductInterface::class
        );

        $this->contentorProductRepository->save($product);
    }

    /**
     * @param int $contentorId
     * @return void
     */
    private function saveTypeEntity($contentorId)
    {
        $this->typeRepository->saveProductContentRequest($contentorId);
    }

    /**
     * @param int $contentorId
     * @param string $status
     */
    private function saveStatusEntity($contentorId, $status)
    {
        $this->statusRepository->saveProductStatus($contentorId, $status);
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    private function getFields(ProductInterface $product)
    {
        /** @var \Contentor\LocalizationApi\Model\Product\ContentCreation\AttributeProvider $attributeProvider */
        $attributeProvider = $this->attributeProviderFactory->create([
            'product' => $product,
            'extraFields' => []
        ]);

        return $attributeProvider->getList();
    }
}
