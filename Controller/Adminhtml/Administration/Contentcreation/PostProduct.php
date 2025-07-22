<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

use Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractPostSingleEntity;
use Magento\Catalog\Model\ProductFactory;

class PostProduct extends AbstractPostSingleEntity
{
    /**
     * @var string
     */
    protected $entityName = 'content creation product';

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
    protected $modelFactory = ProductFactory::class;
}
