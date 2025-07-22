<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller for Content Creation bulk products posting page
 *
 * Displays the admin interface for posting multiple products to Contentor
 * for content creation services.
 */
class PostBulkProducts extends Action
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * PostBulkProducts constructor.
     *
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

    /**
     * Display the content creation bulk products posting interface
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Content Creation | Contentor Bulk Products Post'));

        return $resultPage;
    }
}
