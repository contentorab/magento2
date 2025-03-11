<?php

namespace Contentor\LocalizationApi\Model\Http\Client;

use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Framework\HTTP\Adapter\Curl as CurlAdapter;

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
            'code' => $this->extractCode($response),
            'body' => $this->extractBody($response),
        ];
    }

    /**
     * Extract the response code from a response string
     *
     * @param string $response_str
     * @return int
     */
    protected static function extractCode($response_str)
    {
        preg_match("|^HTTP/[\d\.x]+ (\d+)|", $response_str, $m);

        if (isset($m[1])) {
            return (int) $m[1];
        } else {
            return false;
        }
    }

    /**
     * Extract the body from a response string
     *
     * @param string $response_str
     * @return string
     */
    protected static function extractBody($response_str)
    {
        $parts = preg_split('|(?:\r\n){2}|m', $response_str, 2);
        if (isset($parts[1])) {
            return $parts[1];
        }
        return '';
    }
}
