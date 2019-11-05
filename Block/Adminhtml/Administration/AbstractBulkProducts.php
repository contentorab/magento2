<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use \Magento\Backend\Block\Template\Context;
use \Contentor\LocalizationApi\Helper\ContentorAPI;
use \Magento\Framework\Locale\ListsInterface;
use \Magento\Store\Model\System\Store;
use \Magento\Ui\Component\MassAction\Filter;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class AbstractBulkProducts
 * @package Contentor\LocalizationApi\Block\Adminhtml\Administration
 */
abstract class AbstractBulkProducts extends \Magento\Backend\Block\Template
{
    /**
     * @var int
     */
    protected $_syncType;

    /**
     * @var string
     */
    protected $_template = 'administration/bulkproducts.phtml';
    /**
     * @var
     */
    protected $_btnSubmitLabel;
    /**
     * @var ContentorAPI
     */
    protected $_contentorApi;
    /**
     * @var ListsInterface
     */
    protected $_localeList;
    /**
     * @var Store
     */
    protected $_systemStores;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * AbstractBulkProducts constructor.
     * @param Context $context
     * @param ContentorAPI $contentorApi
     * @param ListsInterface $localeList
     * @param Store $systemStores
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
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

    /**
     * @return array
     */
    public function getLocaleList()
    {
        return $this->_localeList->getOptionLocales();
    }

    /**
     * @return array
     */
    public function getStoreViews()
    {
        return $this->_systemStores->getStoresStructure();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStoreLocale($id)
    {
        return $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductsWithFilter()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $products = $collection->getAllIds();

        return $products;
    }

    /**
     * @return string
     */
    public function getLabelSubmitButton(){
        return $this->_btnSubmitLabel;
    }

    /**
     * @return int
     */
    public function getSyncType(){
        return $this->_syncType;
    }

    /**
     * @return string
     */
    abstract function getSendUrl();
}
