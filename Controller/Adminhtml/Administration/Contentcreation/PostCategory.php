<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

/**
 * Class PostCategory
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation
 */
class PostCategory extends \Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractPostCategory
{
    /**
     * Creatable - content creation should be sent even if source and locale is same
     * @var bool
     */
    protected $shouldValidateTargetAndSource = false;
}
