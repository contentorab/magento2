<?php

namespace Contentor\LocalizationApi\Block\Adminhtml\Administration;

use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ListsInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\System\Store;
use Magento\Ui\Component\MassAction\Filter;

abstract class AbstractBulkProducts extends Template
{
    /**
     * @var int
     */
    protected $syncType;

    /**
     * @var string
     */
    protected $_template = 'administration/bulkproducts.phtml';

    /**
     * @var
     */
    protected $btnSubmitLabel;

    /**
     * @var ListsInterface
     */
    protected $localeList;
    /**
     * @var Store
     */
    protected $systemStores;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * AbstractBulkProducts constructor.
     * @param Context $context
     * @param ListsInterface $localeList
     * @param Store $systemStores
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ConfigurationService $configurationService
     */
    public function __construct(
        Context $context,
        ListsInterface $localeList,
        Store $systemStores,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConfigurationService $configurationService
    ) {
        $this->localeList = $localeList;
        $this->systemStores = $systemStores;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->configurationService = $configurationService;

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getLocaleList(): array
    {
        return $this->localeList->getOptionLocales();
    }

    /**
     * @return array
     */
    public function getStoreViews(): array
    {
        return $this->systemStores->getStoresStructure();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getStoreLocale($id)
    {
        return $this->_scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $id);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getProductsWithFilter(): array
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        return $collection->getAllIds();
    }

    /**
     * @return string
     */
    public function getLabelSubmitButton(): string
    {
        return $this->btnSubmitLabel;
    }

    /**
     * @return int
     */
    public function getSyncType(): int
    {
        return $this->syncType;
    }

    /**
     * @return string
     */
    abstract public function getSendUrl(): string;
}
