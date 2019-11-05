<?php
namespace Contentor\LocalizationApi\Helper;

/**
 * Class DeliveryExpressConfig
 * @package Contentor\LocalizationApi\Helper
 */
class DeliveryExpressConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    private $deliverySpeed = [
        [
            'value' => 'normal',
            'label' => 'Normal'
        ],
        [
            'value' => 'express',
            'label' => 'Express ( Contentor will delivery orders faster )'
        ]
    ];

    /**
     * @return array
     */
    public function getConfig() {
        return $this->deliverySpeed;
    }
}
