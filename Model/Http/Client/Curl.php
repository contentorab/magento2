<?php
namespace Contentor\LocalizationApi\Model\Http\Client;

use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface;
use Magento\Framework\HTTP\Adapter\Curl as CurlAdapter;
use Psr\Log\LoggerInterface;

/**
 * Class Curl
 * @package Contentor\LocalizationApi\Model\Http\Client
 */
class Curl implements HttpClientInterface
{
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
        CurlAdapter $curl,
        LoggerInterface $logger
    ) {
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
        foreach ($transfer->getHeaders() as $name => $value) {
            $headers[] = sprintf('%s: %s', $name, $value);
        }
        $this->curl->write(
            $transfer->getMethod(),
            $transfer->getUri(),
            '1.1',
            $headers,
            $transfer->getBody()
        );

        $this->logger->debug($transfer->getBody());
        $response = $this->curl->read();
        return [
            'code' => \Zend_Http_Response::extractCode($response),
            'body' => \Zend_Http_Response::extractBody($response),
        ];
    }
}
