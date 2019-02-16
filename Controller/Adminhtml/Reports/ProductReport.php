<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Reports;

class ProductReport extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        // Set template
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magento_Reports::report');
        $resultPage->getConfig()->getTitle()->prepend(__('Contentor Product Localization Report'));

        return $resultPage;
    }
}
