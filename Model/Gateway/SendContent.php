<?php
namespace Contentor\LocalizationApi\Model\Gateway;

use Contentor\LocalizationApi\Model\Http\Converter\JsonToArray;
use Contentor\LocalizationApi\Model\Spi\HttpClientInterface;
use Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SendContent
 * @package Contentor\LocalizationApi\Model\Gateway
 *
 * Gateway command for URL [ContentorBaseUrl]/content:PUT.
 * Responsibility : Data transfer, pre format API response result.
 * Used for sending data to contentor
 */
class SendContent
{
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
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * SendContent constructor.
     * @param HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory
     * @param HttpClientInterface $httpClient
     * @param JsonToArray $jsonToArrayConverter
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        HttpRequestTransferInterfaceFactory $httpRequestTransferInterfaceFactory,
        HttpClientInterface $httpClient,
        JsonToArray $jsonToArrayConverter,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->httpClient = $httpClient;
        $this->httpRequestTransferInterfaceFactory = $httpRequestTransferInterfaceFactory;
        $this->jsonToArrayConverter = $jsonToArrayConverter;
        $this->request = $request;
    }

    /**
     * Send data to contentor
     *
     * @param array $data
     * @return int|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $data)
    {
        //TODO: Move version
        $headers =  [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'User-Agent'    => 'ContentorMagento2/0.7.2'
        ];

        /** @var \Contentor\LocalizationApi\Model\Spi\HttpRequestTransferInterface $transfer */
        $transfer = $this->httpRequestTransferInterfaceFactory->create([
            'headers' => $headers,
            'params' => [],
            'body' => json_encode($this->prepareRequest($data)),
            'method' => \Zend_Http_Client::PUT,
            'uri' => 'v1/content'
        ]);

        $result = $this->httpClient->sendRequest($transfer);

        if($result['code'] !== 200) {
            // TODO: Should this throw an exception
        } else {
            $result = $this->jsonToArrayConverter->convert(
                $result['body']
            );

            return $result['id'];
        }
    }

    /**
     * Prepare data for request
     * and make data understandble for API
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    private function prepareRequest($data)
    {
        $request  = [
            'language' => [
                'source' => str_replace('_', '-', $data['sourceLocale']),
                'target' => str_replace('_', '-', $data['targetLocale']),
            ],
            'type' => $data['type'],
            'fields' => $data['fields']
        ];

        if($data['type'] == 'update') {
            if(empty($data['previous'])) {
                throw new LocalizedException('Tried sending update request without previous version');
            } else {
                $request['previous'] = $data['previous'];
            }
        }
        /**
         * Get delivery speed for contentor request
         *
        */
        $deliverySpeedData = $this->getDeliverySpeed();

        if ( !empty($deliverySpeedData) ) {
            $request['preferences'][] = $deliverySpeedData;
        }
        /**
         * Get machine translation for contentor request
         *
        */
        $machineTranslationData = $this->getMachineTranslation();

        if ( !empty($machineTranslationData) ) {
            $request['hints'][] = $machineTranslationData;
        }
        return $request;
    }

    /**
     * Get Delivery speed from dropdown select
     * @return array
     */
    private function getDeliverySpeed() {

        $deliverySpeed = $this->request->getParam('deliverySpeed');

        return [
            'type'  => 'delivery-speed',
            'value' => $deliverySpeed
        ];
    }

    /**
     * Get Machine Translation from dropdown select
     * @return array
     */
    private function getMachineTranslation() {

        $machineTranslation = $this->request->getParam('machineTranslation');
        if(empty($machineTranslation) || $machineTranslation === 'none'){
            return;
        }

        return [
            'type'  => 'machine-translation',
            'policy' => $machineTranslation
        ];
    }
}
