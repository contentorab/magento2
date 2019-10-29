<?php
namespace Contentor\LocalizationApi\Model\Service;

/**
 * Class ContentCreationCategorySendContent
 * @package Contentor\LocalizationApi\Model\Service
 */
class ContentCreationCategorySendContent extends \Contentor\LocalizationApi\Model\Service\AbstractCategorySendContent
{
    /**
     * @var int
     */
    protected $_syncType = \Contentor\LocalizationApi\Model\Category::CONTENT_CREATION_SYNC_TYPE;

    /**
     * @var string
     */
    protected $_syncName = 'content creation';
}
