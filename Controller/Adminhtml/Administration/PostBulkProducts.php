<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class PostBulkProducts extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * PostBulkProducts constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        // Set template
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Contentor Bulk Products Post'));

        return $resultPage;
    }
}
