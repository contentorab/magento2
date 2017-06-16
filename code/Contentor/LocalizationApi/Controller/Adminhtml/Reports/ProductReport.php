<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Reports;

class ProductReport extends \Magento\Framework\App\Action\Action
{
	
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Contentor\LocalizationApi\Helper\ContentorAPI $contentorApi,
			\Psr\Log\LoggerInterface $logger,
			\Magento\Framework\View\Result\PageFactory $pageFactory
			) {
				$this->_contentorApi = $contentorApi;
				$this->_pageFactory = $pageFactory;
				$this->_logger = $logger;
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