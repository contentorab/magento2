<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Form\Fields;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreRepository;

class SourceRenderer extends Select
{
    /**
     * @var StoreRepository
     */
    protected $storeRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

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
        $this->storeRepository = $storeRepository;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $stores = $this->storeRepository->getList();
            $this->addOption('', '');
            foreach ($stores as $store) {
                if ($store->getId()) {
                    $this->addOption($store->getId(), $store->getName());
                }
            }
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
