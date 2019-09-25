<?php
namespace Contentor\LocalizationApi\Model\Service;

/**
 * Class ProductSendContent
 * @package Contentor\LocalizationApi\Model\Service
 */
class ProductSendContent extends \Contentor\LocalizationApi\Model\Service\AbstractProductSendContent
{
    /**
     * @var int
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Product::LOCALIZED_SYNC_TYPE;

    /**
     * @var string
     */
    protected $_syncName = 'localization';
}
