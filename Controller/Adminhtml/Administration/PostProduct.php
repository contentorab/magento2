<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Catalog\Model\ProductFactory;

class PostProduct extends AbstractPostSingleEntity
{
    /**
     * @var string
     */
    protected $entityName = 'localization product';

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
    protected $modelFactory = ProductFactory::class;
}
