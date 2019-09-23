<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Reports;

/**
 * Class ProductReport
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Reports
 */
class ProductReport extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $_pageFactory;

    /**
     * ProductReport constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // Set template
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magento_Reports::report');
        $resultPage->getConfig()->getTitle()->prepend(__('Contentor Product Localization Report'));
        return $resultPage;
    }
}
