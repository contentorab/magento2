<?php
namespace Contentor\LocalizationApi\Model\Service;
/**
 * Class ContentCreationProductSendContent
 * @package Contentor\LocalizationApi\Model\Service
 */
class ContentCreationProductSendContent extends \Contentor\LocalizationApi\Model\Service\AbstractProductSendContent
{
    /**
     * @var int
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Product::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $_syncName = 'content creation';
}
