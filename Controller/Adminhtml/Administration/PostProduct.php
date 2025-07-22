<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Catalog\Model\ProductFactory;

/**
 * Controller for posting products for localization
 *
 * Handles the sending of individual products to Contentor platform
 * for localization services.
 */
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
