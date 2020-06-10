<?php
namespace Contentor\LocalizationApi\Model\Gateway;

use Psr\Log\LoggerInterface;

use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;

/**
 * Class SetImportState
 * @package Contentor\LocalizationApi\Model\Gateway
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
     * @param ConfigurationService $configurationService
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
     * @return array
     */
    public function execute($contentRequest, $state)
    {
        $this->setImportState($contentRequest, $state);
        $this->logger->info('Updated importState for ' . $contentRequest . ', set state: ' . $state);

    }

    /**
     * Updates the importState
     *
     * @param string $contentRequest
     * @param string $state
     */
    private function setImportState($contentRequest, $state)
    {
        $headers =  [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ];
        $body = '{
            "importState": "' . $state . '"
        }';
        /** @var \Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface $transfer */
        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'params' => [],
            'method' => \Zend_Http_Client::PUT,
            'body' => $body,
            'uri' => 'v1/content/'. $contentRequest . '/import'
        ]);
        $this->httpClient->sendRequest($transfer);
    }
}
