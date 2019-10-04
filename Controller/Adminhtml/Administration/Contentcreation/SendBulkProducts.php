<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

/**
 * Class SendBulkProducts
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation
 */
class SendBulkProducts extends \Contentor\LocalizationApi\Controller\Adminhtml\Administration\AbstractSendBulkProducts
{
    /**
     * Creatable - content creation should be sent even if source and locale is same
     * @var bool
     */
    protected $shouldValidateTargetAndSource = false;
}
