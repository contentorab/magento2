<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Service\ProductImport;

use Contentor\LocalizationApi\Model\Product;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use \Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Contentor\LocalizationApi\Api\Data\ProductInterface as ContentorProductInterface;
use Contentor\LocalizationApi\Model\Product\AttributeProviderFactory;
use Contentor\LocalizationApi\Model\ProductRepository;
use Contentor\LocalizationApi\Api\Data\ProductInterfaceFactory;
use Contentor\LocalizationApi\Service\ProductImport\ProductImportSelection;
use Contentor\LocalizationApi\Model\Product\Import\Report;
use Contentor\LocalizationApi\Model\Product\Import\ReportRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductFactory as MagentoProductFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Consumer for importing products
 */
class Consumer
{
    public const REQUEST_BATCH_SIZE = 1000;

    private ProductImportSelection $productImportSelection;

    private AttributeProviderFactory $attributeProviderFactory;
 
    private ConfigurationService $contentorConfig;

    private HttpClientInterface $httpClient;

    private HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory;

    private Json $serializer;

    private DateTime $dateTime;

    private DataObjectHelper $dataObjectHelper;

    private ProductRepository $contentorProductRepository;

    private ProductInterfaceFactory $contentorProductInterfaceFactory;

    private MagentoProductFactory $magentoProductFactory;

    private ReportRepository $reportRepository;

    private ?string $sourceLocale = null;

    private int $syncType = Product::IMPORT_SYNC_TYPE;

    public function __construct(
        ProductImportSelection $productImportSelection,
        AttributeProviderFactory $attributeProviderFactory,
        ConfigurationService $contentorConfig,
        HttpClientInterface $httpClient,
        HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory,
        Json $serializer,
        DateTime $dateTime,
        DataObjectHelper $dataObjectHelper,
        ProductRepository $contentorProductRepository,
        ProductInterfaceFactory $contentorProductInterfaceFactory,
        MagentoProductFactory $magentoProductFactory,
        ReportRepository $reportRepository
    ) {
        $this->productImportSelection = $productImportSelection;
        $this->attributeProviderFactory = $attributeProviderFactory;
        $this->contentorConfig = $contentorConfig;
        $this->httpClient = $httpClient;
        $this->httpRequestTransferInterfaceFactory = $httpRequestTransferInterfaceFactory;
        $this->serializer = $serializer;
        $this->dateTime = $dateTime;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->contentorProductRepository = $contentorProductRepository;
        $this->contentorProductInterfaceFactory = $contentorProductInterfaceFactory;
        $this->magentoProductFactory = $magentoProductFactory;
        $this->reportRepository = $reportRepository;
    }

    /**
     * Processes queued message
     *
     * @param string $instruction Json serialized instruction
     * @return int
     * @throws LocalizedException
     */
    public function process(string $instruction = ''): int
    {
        $instruction = $this->serializer->unserialize($instruction);
        $retryFailed = $instruction['retry_failed'] ?? false;
        $this->sourceLocale = $this->contentorConfig->getSourceLocale();
        $request = ($retryFailed) ? $this->retryFailedSetup() : $this->standardSetup();
        $requestData = $request['request_data'] ?? [];

        if (count($requestData) < 1) {
            return 1;
        }

        $requestDataBatches = array_chunk($requestData, self::REQUEST_BATCH_SIZE);
        $requestMetaDataBatches = array_chunk($request['request_data_meta'], self::REQUEST_BATCH_SIZE);

        foreach ($requestDataBatches as $index => $requestData) {
            $transfer = $this->prepareTransfer($requestData);
            $response = $this->httpClient->sendRequest($transfer);
            $this->handleResponseCode($response);

            $responseBody = $this->serializer->unserialize($response['body'] ?? '');
            $requestMetaData = $requestMetaDataBatches[$index];
            $this->handleResponseContent($responseBody, $requestMetaData);
        }

        return 1;
    }

    /**
     * Setup request data for a standard product data import
     *
     * @return array
     */
    private function standardSetup(): array
    {
        $products = $this->productImportSelection->select();
        if (count($products) < 1) {
            return [];
        }

        $this->sourceLocale = $this->contentorConfig->getSourceLocale();
        $targetStoreViews = $this->contentorConfig->getTargetStoreViews();

        $requestData = [];
        $requestDataMeta = [];
        foreach ($targetStoreViews as $targetStoreView) {
            $targetLocale = $this->contentorConfig->getMainLocale($targetStoreView);
            foreach ($products as $product) {
                $requestData[] = $this->prepareProductData(
                    [
                        'target_locale' => $targetLocale,
                        'store_id' => $targetStoreView
                    ],
                    $product
                );

                $report = $this->reportRepository->getByProductIdAndLocales(
                    (int)$product->getId(),
                    (string)$this->sourceLocale,
                    (string)$targetLocale
                );

                if (!$report->getId()) {
                    $report->setData([
                        'product_id' => $product->getId(),
                        'source_locale' => $this->sourceLocale,
                        'target_locale' => $targetLocale,
                        'target_store_id' => $targetStoreView,
                        'status' => Report::STATUS_PENDING
                    ]);
                    $this->reportRepository->save($report);
                }

                // We store metadata for each request with array indices matching,
                // So they can later be saved as Contentor Product entities
                $requestDataMeta[] = [
                    'product_id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'target_locale' => $targetLocale,
                    'store_id' => $targetStoreView,
                    'report' => $report
                ];
            }
        }

        return [
            'request_data' => $requestData,
            'request_data_meta' => $requestDataMeta
        ];
    }

    /**
     * Setup request data for retrying failed imports
     *
     * @return array
     */
    private function retryFailedSetup(): array
    {
        $reports = $this->productImportSelection->selectForRetry();
        if (count($reports) < 1) {
            return [];
        }

        $requestData = [];
        $requestDataMeta = [];
        foreach ($reports as $report) {
            $targetLocale = $report->getTargetLocale();
            $targetStoreId = $report->getTargetStoreId();

            $product = $this->magentoProductFactory->create();
            $product->setSku($report->getSku());
            $product->setId($report->getProductId());
            $requestData[] = $this->prepareProductData(
                [
                    'target_locale' => $targetLocale,
                    'store_id' => $targetStoreId
                ],
                $product
            );

            $requestDataMeta[] = [
                'product_id' => $report->getProductId(),
                'sku' => $report->getSku(),
                'target_locale' => $targetLocale,
                'store_id' => $targetStoreId,
                'report' => $report
            ];
        }

        return [
            'request_data' => $requestData,
            'request_data_meta' => $requestDataMeta
        ];
    }

    /**
     * Prepare product for request
     *
     * @param array $data
     * @return array
     */
    private function prepareProductData(array $data, \Magento\Catalog\Model\Product $product): array
    {
        $request = [
            'language' => [
                'source' => str_replace('_', '-', $this->sourceLocale),
                'target' => str_replace('_', '-', $data['target_locale']),
            ],
            'type' => 'import',
            'fields' => $this->getProductFields($product, (int)$data['store_id'])
        ];

        return $request;
    }

    /**
     * @param array $requestData
     * @return HttpRequestTransferInterface
     */
    private function prepareTransfer(array $requestData): HttpRequestTransferInterface
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'ContentorMagento2/' . $this->contentorConfig->getVersion()
        ];

        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'body' => $this->serializer->serialize($requestData),
            'method' => \Laminas\Http\Request::METHOD_PUT,
            'uri' => 'v1/content',
            'params' => []
        ]);

        return $transfer;
    }

    /**
     * Saves a contentor_product entity
     *
     * @param string $contentorId
     * @param ProductInterface $product
     * @param string $sourceLocale
     * @param string $targetLocale
     * @param int $targetStoreId
     * @param int $type
     * @return void
     */
    private function saveEntity(
        string $contentorId,
        int $productId,
        string $sku,
        string $sourceLocale,
        string $targetLocale,
        int $targetStoreId,
        int $type
    ): void {
        $contentorProduct = $this->contentorProductInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $contentorProduct,
            [
                ContentorProductInterface::CONTENTOR_ID => $contentorId,
                ContentorProductInterface::M2_PRODUCT_ID => $productId,
                ContentorProductInterface::SKU => $sku,
                ContentorProductInterface::SOURCE_LOCALE => $sourceLocale,
                ContentorProductInterface::TARGET_LOCALE => $targetLocale,
                ContentorProductInterface::TARGET_STORE => $targetStoreId,
                ContentorProductInterface::TYPE => $type,
                ContentorProductInterface::STATE => 'imported',
                ContentorProductInterface::SENT_TIME => $this->dateTime->gmtDate(),
                ContentorProductInterface::SYNCHRONIZE_TYPE => $this->syncType,
                ContentorProductInterface::DELIVERY_SPEED => null,
                ContentorProductInterface::MACHINE_TRANSLATION => 'none'
            ],
            ContentorProductInterface::class
        );

        $this->contentorProductRepository->save($contentorProduct);
    }

    /**
     * @param ProductInterface $product
     * @param int $storeId
     * @return array
     */
    private function getProductFields(ProductInterface $product, int $storeId): array
    {
        $attributeProvider = $this->attributeProviderFactory->create([
            'product' => $product,
            'extraFields' => [],
            'syncType' => $this->syncType,
            'storeId' => $storeId
        ]);

        return $attributeProvider->getImportList();
    }

    /**
     * @param array $response
     * @return void
     * @throws LocalizedException
     */
    private function handleResponseCode(array $response): void
    {
        $responseCode = $response['code'] ?? 0;
        // On response code 400, we want to store the error details in a report,
        //  on other error codes we will terminate.
        if ($responseCode > 299 && (int)$responseCode !== 400) {
            throw new LocalizedException(
                __('Product import attempt resulted in HTTP error code: %1', $responseCode)
            );
        }

        if ($responseCode === 0) {
            throw new LocalizedException(
                __('No response received')
            );
        }
    }

    /**
     * Saves report data and, on successful import, also the import data in the contentor_product table
     *
     * @param array $response
     * @param array $requestDataMeta
     * @return void
     */
    private function handleResponseContent(array $response, array $requestDataMeta): void
    {
        // First check for errors. On any error in a batch request, the whole batch was rejected
        $errors = $response['errors'] ?? [];
        if (count($errors) > 0) {
            foreach ($response['errors'] as $errorDetail) {
                $errorMessages = $this->serializer->serialize($errorDetail['errors'] ?? '');
                $index = $errorDetail['request'];
                $metaData = $requestDataMeta[$index];
                $report = $metaData['report'];
                /** @var \Contentor\LocalizationApi\Model\Product\Import\Report $report */
                $report->setStatus(Report::STATUS_ERROR);
                $report->setMessage($errorMessages);
                $this->reportRepository->save($report);
            }

            return;
        }

        if (count($requestDataMeta) === 1) {
            // If batch request contained only one product,
            //  response will be a single object instead of an array
            $response['requests'] = [$response];
        }

        foreach ($response['requests'] ?? [] as $index => $responseDetail) {
            $metaData = $requestDataMeta[$index];
            $report = $metaData['report'];
            /** @var \Contentor\LocalizationApi\Model\Product\Import\Report $report */
            $this->saveEntity(
                $responseDetail['id'],
                (int)$metaData['product_id'],
                (string)$metaData['sku'],
                (string)$this->sourceLocale,
                (string)$metaData['target_locale'],
                (int)$metaData['store_id'],
                (int)$this->syncType
            );

            $report->setStatus(Report::STATUS_SUCCESS);
            $report->setMessage(sprintf('Contentor ID: %s', $responseDetail['id']));
            $this->reportRepository->save($report);
        }
    }
}
