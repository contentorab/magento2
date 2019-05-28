<?php
namespace Contentor\LocalizationApi\Model\Gateway;

use Contentor\LocalizationApi\Model\Http\Converter\JsonToArray;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;

/**
 * Class SendContent
 * @package Contentor\LocalizationApi\Model\Gateway
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
    private $configurationService;

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
     * SendContent constructor.
     * @param ConfigurationService $configurationService
     * @param HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory
     * @param HttpClientInterface $httpClient
     * @param JsonToArray $jsonToArrayConverter
     */
    public function __construct(
        ConfigurationService $configurationService,
        HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory,
        HttpClientInterface $httpClient,
        JsonToArray $jsonToArrayConverter
    ) {
        $this->configurationService = $configurationService;
        $this->httpClient = $httpClient;
        $this->httpRequestTransferInterfaceFactory = $httpRequestTransferInterfaceFactory;
        $this->jsonToArrayConverter = $jsonToArrayConverter;
    }

    /**
     * Send data to contentor
     *
     * @param array $data
     * @return int|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $data)
    {
        $headers =  [
            'Content-Type'  => 'application/json',
            'Authorization' =>  'Bearer '. $this->configurationService->getToken(),
            'Accept'        => 'application/json'
        ];
        /** @var \Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface $transfer */
        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'params' => [],
            'body' => json_encode($this->prepareRequest($data)),
            'method' => \Zend_Http_Client::PUT,
            'uri' => sprintf('%s/content', $this->configurationService->getApiBaseUrl())
        ]);

        $result = $this->httpClient->sendRequest($transfer);
        $result = $this->jsonToArrayConverter->convert(
            $result['body']
        );

        if (!isset($result['id'])) {
            // No ID returned
            $contentorId = 'No ID returned';
        } else {
            $contentorId = $result['id'];
        }

        return $contentorId;
    }

    /**
     * Prepare data for request
     * and make data understandble for API
     *
     * @param array $data
     * @return array
     */
    private function prepareRequest($data)
    {
        $request  = [
            'language' => [
                'source' => str_replace('_', '-', $data['sourceLocale']),
                'target' => str_replace('_', '-', $data['targetLocale']),
            ],
            'type' => $data['type'],
            'fields' => $data['fields']
        ];

        if (!empty($data['previd']) && $data['type'] == 'update') {
            $request['previous'] = $data['previd'];
        }

        return $request;
    }
}
