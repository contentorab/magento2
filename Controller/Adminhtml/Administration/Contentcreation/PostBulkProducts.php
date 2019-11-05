<?php
namespace Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class PostBulkProducts
 * @package Contentor\LocalizationApi\Controller\Adminhtml\Administration\Contentcreation
 */
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

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Content Creation | Contentor Bulk Products Post'));

        return $resultPage;
    }
}
