<?php
namespace Contentor\LocalizationApi\Model\Gateway;

use Contentor\LocalizationApi\Model\Http\Converter\JsonToArray;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;

/**
 * Class GetUpdates
 * @package Contentor\LocalizationApi\Model\Gateway
 */
class GetUpdates
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
     * @param string $date
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($date)
    {
        $result = $this->getUpdates($date);
        $updates = [];
        if ($result['total'] > 0) {
            // Process results from page 1
            foreach ($result['requests'] as $request) {
                $updates[] = $request;
            }
        }
        if ($result['pages'] > 1) {
            for ($i = 2; $i <= $result['pages']; $i++) {
                $nextPage = $this->getUpdates($date, $i);
                foreach ($nextPage['requests'] as $request) {
                    $updates[] = $request;
                }
            }
        }

        return $updates;
    }

    /**
     * @param $date
     * @param null $page
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getUpdates($date, $page = null)
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
            'method' => \Zend_Http_Client::GET,
            'uri' => sprintf('%scontent?%s%s',
                $this->configurationService->getApiBaseUrl(),
                $this->buildSearchCriteria($date),
                (null === $page) ? '' : '&page=' . $page

            )
        ]);

        $result = $this->httpClient->sendRequest($transfer);
        $result = $this->jsonToArrayConverter->convert(
            $result['body']
        );

        return $result;
    }

    /**
     * @param string $date
     * @return string
     */
    private function buildSearchCriteria($date)
    {
        $args = [
            'criteria' => [
                [
                    'type' => 'modified',
                    'criteria' => [
                        'from' => $date,
                        'to' => 'tomorrow'
                    ]
                ]
            ],
            'sortBy' => ['created:desc']
        ];

        return http_build_query(
            ['query' => json_encode($args)]
        );
    }
}