<?php
namespace Contentor\LocalizationApi\Helper;

class VersioningConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    private $versioning= [
        [
            'value' => 'default',
            'label' => 'default'
        ],
        [
            'value' => 'yes',
            'label' => 'Yes'
        ],
        [
            'value' => 'no',
            'label' => 'No'
        ],
    ];

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->versioning;
    }
}
