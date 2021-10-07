<?php

namespace Contentor\LocalizationApi\Model\Gateway;

use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Psr\Log\LoggerInterface;
use Zend_Http_Client;

/**
 * Class SetImportState
 *
 * Gateway command for setting the importState after a successful or failed import
 * of content.
 */
class SetImportState
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * @var HttpRequestTransferInterfaceFactory
     */
    private $httpRequestTransferInterfaceFactory;

    /**
     * SetImportState constructor.
     * @param Logger $logger
     * @param HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory
     * @param HttpClientInterface $httpClient
     */
    public function __construct(
        Logger $logger,
        HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory,
        HttpClientInterface $httpClient
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->httpRequestTransferInterfaceFactory = $httpRequestTransferInterfaceFactory;
    }

    /**
     * Set the importState for the request.
     *
     * @param string $contentRequest
     * @param string $state
     * @return void
     */
    public function execute($contentRequest, $state): void
    {
        $this->setImportState($contentRequest, $state);
        $this->logger->info('Updated importState for ' . $contentRequest . ', set state: ' . $state);
    }

    /**
     * Updates the importState
     *
     * @param string $contentRequest
     * @param string $state
     * @return void
     */
    private function setImportState($contentRequest, $state): void
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        $body = '{
            "importState": "' . $state . '"
        }';
        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'params' => [],
            'method' => Zend_Http_Client::PUT,
            'body' => $body,
            'uri' => 'v1/content/' . $contentRequest . '/import'
        ]);
        $this->httpClient->sendRequest($transfer);
    }
}
