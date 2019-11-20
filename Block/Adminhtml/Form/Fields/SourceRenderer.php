<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreRepository;

/**
 * Class SourceRenderer
 * @package Contentor\LocalizationApi\Block\Adminhtml\Form\Fields
 */
class SourceRenderer extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var StoreRepository
     */
    protected $_storeRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * SourceRenderer constructor.
     * @param Context $context
     * @param StoreRepository $storeRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreRepository $storeRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeRepository = $storeRepository;
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $stores = $this->_storeRepository->getList();
            $this->addOption('', '');
            foreach ($stores as $store) {
                if ($store->getId()) {
                    $locale = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
                    $this->addOption($store->getId(), $store->getName());
                }
            }
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param  string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
