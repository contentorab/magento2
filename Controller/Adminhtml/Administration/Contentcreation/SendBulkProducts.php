<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

use Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractSendBulkProducts;

/**
 * Controller for sending bulk products for content creation
 *
 * Extends AbstractSendBulkProducts to handle bulk product sending
 * to Contentor platform for content creation services.
 */
class SendBulkProducts extends AbstractSendBulkProducts
{
    /**
     * Creatable - content creation should be sent even if source and locale is same
     * @var bool
     */
    protected $shouldValidateTargetAndSource = false;
}
