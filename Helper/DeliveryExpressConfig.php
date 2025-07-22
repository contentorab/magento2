<?php
namespace Contentor\LocalizationApi\Helper;

/**
 * Class DeliveryExpressConfig
 *
 * Helper for delivery express configuration options
 */
class DeliveryExpressConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Available delivery speed options
     *
     * @var array
     */
    private $deliverySpeed = [
        [
            'value' => 'normal',
            'label' => 'Normal'
        ],
        [
            'value' => 'express',
            'label' => 'Express'
        ]
    ];

    /**
     * Get delivery speed configuration options
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->deliverySpeed;
    }
}
