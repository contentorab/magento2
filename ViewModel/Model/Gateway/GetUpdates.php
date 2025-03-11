<?php

namespace Contentor\LocalizationApi\Model\Gateway;

use Contentor\LocalizationApi\Model\Http\Converter\JsonToArray;
use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class GetUpdates
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
     * @var LoggerInterface
     */
    private $psrLogger;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * GetUpdates constructor.
     * @param Logger $logger
     * @param HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory
     * @param HttpClientInterface $httpClient
     * @param JsonToArray $jsonToArrayConverter
     * @param LoggerInterface $psrLogger
     * @param Json $serializer
     */
    public function __construct(
        Logger $logger,
        HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory,
        HttpClientInterface $httpClient,
        JsonToArray $jsonToArrayConverter,
        LoggerInterface $psrLogger,
        Json $serializer
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->httpRequestTransferInterfaceFactory = $httpRequestTransferInterfaceFactory;
        $this->jsonToArrayConverter = $jsonToArrayConverter;
        $this->psrLogger = $psrLogger;
        $this->serializer = $serializer;
    }

    /**
     * Recursively get updates for all content types.
     *
     * @param string $date
     * @return array
     */
    public function execute($date, $page = null): array
    {
        $result = $this->getUpdates($date, $page);
        $updates_data = [];
        foreach ($result['requests'] as $request) {
            $updates_data[] = $request;
        }
        

        // Output some information to the log about how many updated requests where found
        $this->logger->info('Found ' . count($updates_data) . ' updated requests since ' . $date);

        $updates["data"] = $updates_data;
        $updates["pagination"] = [
            'page' => $result["page"],
            'pages' => $result["pages"],
            'total' => $result["total"],
        ];

        return $updates;
    }

    /**
     * Returns data by page.
     *
     * @param string $date
     * @param null $page
     * @return array
     */
    private function getUpdates($date, $page = null): array
    {
        $body = [];
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            $transfer = $this->httpRequestTransferInterfaceFactory->create([
                'headers' => $headers,
                'params' => [],
                'method' => \Laminas\Http\Request::METHOD_GET,
                'body' => null,
                'uri' => sprintf(
                    'v1/content?%s%s',
                    $this->buildSearchCriteria($date),
                    (null === $page) ? '' : '&page=' . $page
                )
            ]);

            $result = $this->httpClient->sendRequest($transfer);
            $body = $this->jsonToArrayConverter->convert(
                $result['body']
            );
        } catch (\Exception $e) {
            $this->psrLogger->error($e->getMessage());
        }
        return $body;
    }

    /**
     * @param string $date
     * @return string
     */
    private function buildSearchCriteria($date): string
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
            'sortBy' => ['lastStateChange:asc']
        ];

        return http_build_query(
            ['query' => $this->serializer->serialize($args)]
        );
    }
}
