<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

/**
 * Class PostProduct
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation
 */
class PostProduct extends \Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractPostProduct
{
    /**
     * Creatable - content creation should be sent even if source and locale is same
     * @var bool
     */
    protected $shouldValidateTargetAndSource = false;
}
