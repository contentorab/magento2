<?php
namespace Contentor\LocalizationApi\Model\Service;

/**
 * Class CategorySendContent
 * @package Contentor\LocalizationApi\Model\Service
 */
class CategorySendContent extends \Contentor\LocalizationApi\Model\Service\AbstractCategorySendContent
{
    /**
     * @var int
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Category::LOCALIZED_SYNC_TYPE;

    /**
     * @var string
     */
    protected $_syncName = 'localization';
}
