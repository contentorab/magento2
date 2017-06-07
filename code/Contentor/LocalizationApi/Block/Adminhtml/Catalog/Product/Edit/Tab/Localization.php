<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab;
 
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Locale\ListsInterface;
use Magento\Store\Model\System\Store;
 
class Localization extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'product/edit/localization.phtml';
 
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;
    protected $_resolver;
    protected $_systemStores;
 
    public function __construct(
        Context $context,
        Registry $registry,
    	ListsInterface $localeList,
    	Store	$systemStores,
        array $data = []
    )
    {
    	$this->_coreRegistry = $registry;
    	$this->_localeList = $localeList;
    	$this->_systemStores = $systemStores;
    	//$this->_logger->addDebug($systemStores);
        parent::__construct($context, $data);
    }
 
    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
    
    public function getLocales()
    {
    	return $this->_localeList->getOptionLocales();
    }
    
    public function getStoreViews()
    {
    	return $this->_systemStores->getStoresStructure();
    }
 
}