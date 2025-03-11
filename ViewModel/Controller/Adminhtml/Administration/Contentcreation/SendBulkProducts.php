<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

use Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractSendBulkProducts;

class SendBulkProducts extends AbstractSendBulkProducts
{
    /**
     * Creatable - content creation should be sent even if source and locale is same
     * @var bool
     */
    protected $shouldValidateTargetAndSource = false;
}
