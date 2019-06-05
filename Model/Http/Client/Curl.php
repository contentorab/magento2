<?php
namespace Contentor\LocalizationApi\Model\Http\Client;

use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Framework\HTTP\Adapter\Curl as CurlAdapter;
use Psr\Log\LoggerInterface;

/**
 * Class Curl
 * @package Contentor\LocalizationApi\Model\Http\Client
 */
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CurlAdapter $curl
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigurationService $configurationService,
        CurlAdapter $curl,
        LoggerInterface $logger
    ) {
        $this->configurationService = $configurationService;
        $this->curl = $curl;
        $this->logger = $logger;
    }

    /**
     * @param HttpRequestTransferInterface $transfer
     * @return array
     */
    public function sendRequest(HttpRequestTransferInterface $transfer)
    {
        $headers = [];
        $token = $this->configurationService->getToken();
        if (! empty($token)) {
            // Apply the authorization token if it is set
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
        $result = [
            'code' => \Zend_Http_Response::extractCode($response),
            'body' => \Zend_Http_Response::extractBody($response),
        ];

        $this->logger->debug('Got response via API', $result);

        return $result;
    }
}
