<?php
namespace Contentor\LocalizationApi\Model\Gateway;

use Psr\Log\LoggerInterface;

use Contentor\LocalizationApi\Model\Http\Converter\JsonToArray;
use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Contentor\LocalizationApi\Service\ConfigurationService;

/**
 * Class GetUpdates
 * @package Contentor\LocalizationApi\Model\Gateway
 *
 * Gateway command for URL [ContentorBaseUrl]/content:GET.
 * Responsibility : Data transfer, pre format API response result.
 * Used for getting updates from contentor
 */
class GetUpdates
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
        Logger $logger,
        HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory,
        HttpClientInterface $httpClient,
        JsonToArray $jsonToArrayConverter
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->httpRequestTransferInterfaceFactory = $httpRequestTransferInterfaceFactory;
        $this->jsonToArrayConverter = $jsonToArrayConverter;
    }

    /**
     * Recursively get updates for all content types.
     *
     * @param string $date
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($date)
    {
        $result = $this->getUpdates($date);
        $updates = [];
        foreach ($result['requests'] as $request) {
            $updates[] = $request;
        }

        // Output some information to the log about how many updated requests where found
        $this->logger->info('Found ' . count($updates) . ' updated requests since ' . $date);

        return $updates;
    }

    /**
     * Returns data by page.
     *
     * @param string $date
     * @param null $page
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getUpdates($date, $page = null)
    {
        $headers =  [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json'
        ];
        /** @var \Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface $transfer */
        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'params' => [],
            'method' => \Zend_Http_Client::GET,
            'body' => null,
            'uri' => sprintf('v1/content?%s%s',
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
                    'type' => 'lastStateChange',
                    'criteria' => [
                        'from' => $date,
                        'fromInclusive' => false
                    ]
                ]
            ],
            'sortBy' => [ 'lastStateChange:asc' ]
        ];

        return http_build_query(
            ['query' => json_encode($args)]
        );
    }
}
