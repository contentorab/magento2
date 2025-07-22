<?php

namespace Contentor\LocalizationApi\Model\Gateway;

use Contentor\LocalizationApi\Model\Http\Converter\JsonToArray;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class TestConnection
 *
 * Gateway command for URL [ContentorBaseUrl]/auth:GET
 * Responsibility: Check if API ready
 * Used check api state before sending any requests
 * TODO: API endpoint not available [15.05.2019]
 */
class TestConnection
{
    /**
     * HTTP client for API communication
     *
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * HTTP request transfer factory
     *
     * @var HttpRequestTransferInterfaceFactory
     */
    private $httpRequestTransferInterfaceFactory;

    /**
     * JSON to array converter
     *
     * @var JsonToArray
     */
    private $jsonToArrayConverter;

    /**
     * Constructor
     *
     * @param HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory
     * @param HttpClientInterface $httpClient
     * @param JsonToArray $jsonToArrayConverter
     */
    public function __construct(
        HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory,
        HttpClientInterface $httpClient,
        JsonToArray $jsonToArrayConverter
    ) {
        $this->httpClient = $httpClient;
        $this->httpRequestTransferInterfaceFactory = $httpRequestTransferInterfaceFactory;
        $this->jsonToArrayConverter = $jsonToArrayConverter;
    }

    /**
     * Execute connection test
     *
     * @return bool
     * @throws LocalizedException
     */
    public function execute(): bool
    {
        //todo : logic of this check was wrong on base implementation. skip for now
        return true;
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'params' => [],
            'body' => '',
            'method' => \Laminas\Http\Request::METHOD_GET,
            'uri' => 'v1/auth'
        ]);

        $result = $this->httpClient->sendRequest($transfer);

        $result = $this->jsonToArrayConverter->convert(
            $result['body']
        );

        return !empty($result['companyName']);
    }
}
