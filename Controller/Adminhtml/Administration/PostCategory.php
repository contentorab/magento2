<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

/**
 * Class PostCategory
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration
 */
class PostCategory extends \Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractPostSingleEntity
{
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
