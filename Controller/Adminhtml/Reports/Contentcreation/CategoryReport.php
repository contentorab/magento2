<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Reports\Contentcreation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller for Category Content Creation Report page
 *
 * Displays the admin interface for viewing category content creation reports
 * from Contentor platform.
 */
class CategoryReport extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * CategoryReport constructor.
     *
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
     * Display the category content creation report page
     *
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
