<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use \Magento\Backend\Block\Template\Context;
use \Contentor\LocalizationApi\Helper\ContentorAPI;
use \Magento\Framework\Locale\ListsInterface;
use \Magento\Store\Model\System\Store;
use \Magento\Ui\Component\MassAction\Filter;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class BulkProducts extends \Magento\Backend\Block\Template
{

    public function __construct(
        Context $context,
        ContentorAPI $contentorApi,
        ListsInterface $localeList,
        Store $systemStores,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_contentorApi = $contentorApi;
        $this->_localeList = $localeList;
        $this->_systemStores = $systemStores;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    public function getLocaleList()
    {
        return $this->_localeList->getOptionLocales();
    }

    public function getStoreViews()
    {
        return $this->_systemStores->getStoresStructure();
    }

    public function getStoreLocale($id)
    {
        return $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id);
    }

    public function getProductsWithFilter()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $products = $collection->getAllIds();

        return $products;
    }
}
