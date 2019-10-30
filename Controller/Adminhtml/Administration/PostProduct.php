<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

/**
 * Class PostProduct
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration
 */
class PostProduct extends \Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractPostSingleEntity
{
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
