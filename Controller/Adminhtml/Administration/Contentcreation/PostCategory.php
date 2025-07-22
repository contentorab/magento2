<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

use Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractPostSingleEntity;
use Magento\Catalog\Model\CategoryFactory;

/**
 * Controller for posting categories for content creation
 *
 * Handles the sending of individual categories to Contentor platform
 * for content creation services.
 */
class PostCategory extends AbstractPostSingleEntity
{
    /**
     * @var string
     */
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
    protected $modelFactory = CategoryFactory::class;
}
