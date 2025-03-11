<?php

namespace Contentor\LocalizationApi\Controller\Adminhtml\Reports;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Page;

/**
 * Product Import Report Grid Controller
 */
class ProductImportReport extends Action
{
    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        /** @var Page $resultPage */
        $resultPage->setActiveMenu('Magento_Reports::report');
        $resultPage->getConfig()->getTitle()->prepend(__('Contentor Product Import Report'));
        return $resultPage;
    }
}
