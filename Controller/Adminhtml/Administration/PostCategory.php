<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Catalog\Model\CategoryFactory;

class PostCategory extends AbstractPostSingleEntity
{
    /**
     * @var string
     */
    protected $entityName = 'localization category';

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
    protected $modelFactory = CategoryFactory::class;
}
