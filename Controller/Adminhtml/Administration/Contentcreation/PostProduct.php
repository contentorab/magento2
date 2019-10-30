<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;


/**
 * Class PostProduct
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation
 */
class PostProduct extends \Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractPostSingleEntity
{
    /**
     * Creatable - content creation should be sent even if source and locale is same
     * @var bool
     */
    protected $shouldValidateTargetAndSource = false;

    /**
     * @var string
     */
    protected $paramRequestId = 'productid';

    /**
     * @var string
     */
    protected $routeRedirect = 'catalog/product/edit';

    /**
     * @var string
     */
    protected $modelFactory  = 'Magento\Catalog\Model\ProductFactory';
}
