<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

/**
 * Class PostCategory
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation
 */
class PostCategory extends \Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractPostSingleEntity
{
    protected $entityName = 'content creation category';
    /**
     * Creatable - content creation should be sent even if source and locale is same
     * @var bool
     */
    protected $shouldValidateTargetAndSource = false;

    /**
     * @var string
     */
    protected $paramRequestId = 'categoryid';

    /**
     * @var string
     */
    protected $routeRedirect = 'catalog/category/edit';

    /**
     * @var string
     */
    protected $modelFactory  = 'Magento\Catalog\Model\CategoryFactory';
}
