<?php

namespace Contentor\LocalizationApi\Model\Http\Client;

use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Framework\HTTP\Adapter\Curl as CurlAdapter;
use Zend_Http_Response;

class Curl implements HttpClientInterface
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var CurlAdapter
     */
    private $curl;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Curl constructor.
     * @param ConfigurationService $configurationService
     * @param CurlAdapter $curl
     * @param Logger $logger
     */
    public function __construct(
        ConfigurationService $configurationService,
        CurlAdapter $curl,
        Logger $logger
    ) {
        $this->configurationService = $configurationService;
        $this->curl = $curl;
        $this->logger = $logger;
    }

    /**
     * @param HttpRequestTransferInterface $transfer
     * @return array
     */
    public function sendRequest(HttpRequestTransferInterface $transfer): array
    {
        $headers = [
            'User-Agent' => 'ContentorMagento2/' . $this->configurationService->getVersion()
        ];
        $token = $this->configurationService->getToken();
        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        foreach ($transfer->getHeaders() as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value);
        }

        $url = $this->configurationService->getApiBaseUrl() . $transfer->getUri();

        $this->logger->debug('Sending request to API', [
            'method' => $transfer->getMethod(),
            'url' => $url,
            'body' => $transfer->getBody()
        ]);

        $this->curl->write(
            $transfer->getMethod(),
            $url,
            '1.1',
            $headers,
            $transfer->getBody()
        );

        $response = $this->curl->read();
        return [
            'code' => Zend_Http_Response::extractCode($response),
            'body' => Zend_Http_Response::extractBody($response),
        ];
    }
}
