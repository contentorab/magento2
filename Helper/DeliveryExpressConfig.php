<?php
namespace Contentor\LocalizationApi\Helper;

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
            'label' => 'Express'
        ]
    ];

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->deliverySpeed;
    }
}
