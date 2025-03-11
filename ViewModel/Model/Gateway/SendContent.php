<?php

namespace Contentor\LocalizationApi\Model\Gateway;

use Contentor\LocalizationApi\Model\Http\Converter\JsonToArray;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class SendContent
 *
 * Gateway command for URL [ContentorBaseUrl]/content:PUT.
 * Responsibility : Data transfer, pre format API response result.
 * Used for sending data to contentor
 */
class SendContent
{
    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var HttpRequestTransferInterfaceFactory
     */
    private $httpRequestTransferInterfaceFactory;

    /**
     * @var JsonToArray
     */
    private $jsonToArrayConverter;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Json
     */
    private $serializer;


    /**
     * SendContent constructor.
     * @param HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory
     * @param HttpClientInterface $httpClient
     * @param JsonToArray $jsonToArrayConverter
     * @param RequestInterface $request
     * @param ConfigurationService $configurationService
     * @param Json $serializer
     */
    public function __construct(
        HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory,
        HttpClientInterface $httpClient,
        JsonToArray $jsonToArrayConverter,
        RequestInterface $request,
        ConfigurationService $configurationService,
        Json $serializer
    ) {
        $this->httpClient = $httpClient;
        $this->httpRequestTransferInterfaceFactory = $httpRequestTransferInterfaceFactory;
        $this->jsonToArrayConverter = $jsonToArrayConverter;
        $this->request = $request;
        $this->configurationService = $configurationService;
        $this->serializer = $serializer;
    }

    /**
     * Send data to contentor
     *
     * @param array $data
     * @return int|string
     * @throws LocalizedException
     */
    public function execute(array $data)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'ContentorMagento2/' . $this->configurationService->getVersion()
        ];

        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'params' => [],
            'body' => $this->serializer->serialize($this->prepareRequest($data)),
            'method' => \Laminas\Http\Request::METHOD_PUT,
            'uri' => 'v1/content'
        ]);

        $response = $this->httpClient->sendRequest($transfer);

        if ($response['code'] !== 200) {
            throw new LocalizedException(
                __('Something went wrong when the request was sent to Contentor: %1', $response['body'])
            );
        } else {
            $response = $this->jsonToArrayConverter->convert(
                $response['body']
            );

            return $response['id'];
        }
    }

    /**
     * Prepare data for request
     * and make data understandble for API
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    private function prepareRequest($data): array
    {
        $request = [
            'language' => [
                'source' => str_replace('_', '-', $data['sourceLocale']),
                'target' => str_replace('_', '-', $data['targetLocale']),
            ],
            'type' => $data['type'],
            'fields' => $data['fields']
        ];

        if ($data['type'] == 'update') {
            if (empty($data['previous'])) {
                throw new LocalizedException(__('Tried sending update request without previous version'));
            } else {
                $request['previous'] = $data['previous'];
            }
        }

        $deliverySpeedData = $this->getDeliverySpeed();

        if (!empty($deliverySpeedData)) {
            $request['preferences'][] = $deliverySpeedData;
        }

        $machineTranslationData = $this->getMachineTranslation();

        if (!empty($machineTranslationData)) {
            $request['hints'][] = $machineTranslationData;
        }
        return $request;
    }

    /**
     * Get Delivery speed from dropdown select
     * @return array
     */
    private function getDeliverySpeed(): array
    {
        $deliverySpeed = $this->request->getParam('deliverySpeed');

        return [
            'type' => 'delivery-speed',
            'value' => $deliverySpeed
        ];
    }

    /**
     * Get Machine Translation from dropdown select
     * @return array
     */
    private function getMachineTranslation(): array
    {
        $result = [];
        $machineTranslation = $this->request->getParam('machineTranslation');
        if (!empty($machineTranslation) && $machineTranslation !== 'none') {
            $result = [
                'type' => 'machine-translation',
                'policy' => $machineTranslation
            ];
        }

        return $result;
    }
}
