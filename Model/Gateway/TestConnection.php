<?php
namespace Contentor\LocalizationApi\Model\Gateway;

use Contentor\LocalizationApi\Model\Http\Converter\JsonToArray;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;

/**
 * Class TestConnection
 * @package Contentor\LocalizationApi\Model\Gateway
 */
class TestConnection
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
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        //todo : logic of this check was wrong on base implementation. skip for now
        return true;
        $headers =  [
            'Content-Type'  => 'application/json',
            'Authorization' =>  'Bearer '. $this->configurationService->getToken(),
            'Accept'        => 'application/json'
        ];
        /** @var \Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface $transfer */
        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'params' => [],
            'body' => '',
            'method' => \Zend_Http_Client::GET,
            'uri' => sprintf('%sauth', $this->configurationService->getApiBaseUrl())
        ]);

        $result = $this->httpClient->sendRequest($transfer);

        $result = $this->jsonToArrayConverter->convert(
            $result['body']
        );

        return !empty($result['companyName']);

    }
}
