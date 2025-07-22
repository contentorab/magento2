<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller for bulk products posting page
 *
 * Displays the admin interface for posting multiple products to Contentor
 * for localization services.
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
     * Display the bulk products posting interface
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // Set template
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Contentor Bulk Products Post'));

        return $resultPage;
    }
}
