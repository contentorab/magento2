<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Reports\Contentcreation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class CategoryReport extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * ProductReport constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        // Set template
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Magento_Reports::report');
        $resultPage->getConfig()->getTitle()->prepend(__('Contentor Category | Content Creation Report'));
        return $resultPage;
    }
}
