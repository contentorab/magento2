<?php
namespace Contentor\LocalizationApi\Block\Adminhtml\Catalog\Category\Edit\Tab;

use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Locale\ListsInterface;
use Magento\Store\Model\System\Store;
use Magento\Framework\App\ResourceConnection;

/**
 * Class AbstractTabBlock
 * @package Contentor\LocalizationApi\Block\Adminhtml\Catalog\Product\Edit\Tab
 */
abstract class AbstractTabBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'category/edit/contentor_tab.phtml';

    /**
     * @var int
     */
    protected $_syncType;

    /**
     * @var string
     */
    protected $_submitLabelBtn;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var
     */
    protected $_resolver;

    /**
     * @var Store
     */
    protected $_systemStores;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var ListsInterface
     */
    protected $_localeList;

    /**
     * @var ConfigurationService
     */
    protected $configurationService;

    /**
     * Localization constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ListsInterface $localeList
     * @param Store $systemStores
     * @param ResourceConnection $resource
     * @param ConfigurationService $configurationService
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ListsInterface $localeList,
        Store $systemStores,
        ResourceConnection    $resource,
        ConfigurationService $configurationService,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_localeList = $localeList;
        $this->_systemStores = $systemStores;
        $this->_resource = $resource;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
        $this->configurationService = $configurationService;
    }

    /**
     * Retrieve category
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * @return array
     */
    public function getLocales()
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
     * @param string $id
     * @return string
     */
    public function getStoreLocale($id)
    {
        return $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $id);
    }

    /**
     * @param string $sku
     * @return array
     */
    public function getRequestStatus($sku)
    {
        $connection = $this->_resource->getConnection('core_read');
        $statustable = $table = $this->_resource->getTableName('contentor_status');
        $producttable = $table = $this->_resource->getTableName('contentor_products');
        $query = "SELECT `" . $producttable . "`.`contentor_id`, `" . $producttable . "`.`target_store`, `" . $producttable . "`.`sent_time`, `" . $producttable . "`.`completed_time`, `" . $producttable . "`.`canceled_time`, `" . $statustable . "`.`status_time`, `" . $statustable . "`.`status`, `" . $producttable . "`.`state`, `" . $producttable . "`.`type` FROM `" . $producttable . "` LEFT JOIN `" . $statustable . "` ON `" . $producttable . "`.`contentor_id`= `" . $statustable . "`.`contentor_id` WHERE `" . $producttable . "`.`sku` = :this_sku AND `synchronize_type` = :this_synchronize_type ORDER BY `" . $producttable . "`.`contentor_id` DESC, `" . $statustable . "`.`status_time`";
        $binds = [
            'this_sku' => $sku,
            'this_synchronize_type' => $this->_syncType
        ];
        $status = $connection->fetchAll($query, $binds);

        return $status;
    }

    /**
     * @return array
     */
    public function getValidationMessages()
    {
        $result = $this->configurationService->validateCategoryConfiguration();
        return $result['messages'];
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        $result = $this->configurationService->validateCategoryConfiguration();
        return !$result['error'];
    }

    /**
     * @return string
     */
    public function getLabelSubmitButton(){
        return $this->_submitLabelBtn;
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
    abstract function getSubmitUrl();
}
