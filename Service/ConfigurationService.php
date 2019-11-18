<?php
namespace Contentor\LocalizationApi\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigurationService
 * @package Contentor\LocalizationApi\Service
 *
 * Contains all accessors to configurable extension options (api credentials, general settings)
 */
class ConfigurationService
{
    /**#@+
     * Admin config XML Path constants
     * @var string
     */
    const XML_PATH_TOKEN = 'contentor_options/token/apitoken';
    const XML_PATH_AUTOMATION_ENABLE = 'contentor_options/automation/import';
    const XML_PATH_SOURCE_LOCALE = 'contentor_options/source/sourcelocale';
    const XML_PATH_TARGET_STORE_VIEWS = 'contentor_options/targets/targetviews';
    const XML_PATH_PRODUCT_FIELDS = 'contentor_options/fieldDetails/productFieldDetails';
    const XML_PATH_DEVELOPER_MODE = 'contentor_options/fieldDetails/developer_mode';
    const XML_PATH_API_BASE_URL = 'contentor_options/contentor_options/api_base_url';
    const XML_PATH_VERSIONING_ENABLED = 'contentor_options/versioning/versioning_enable';
    const XML_PATH_MAIN_LOCALE = 'general/locale/code';
    const XML_PATH_CONTENT_CREATION_FIELDS = 'contentor_options/contentCreation/fieldDetails';
    const XML_LIMITATION_REQUESTS = 'contentor_options/limitation/requests';
    /**#@-*/

    /**#@+
     * Category config XML Path constants
     * @var string
     */
    const XML_PATH_CATEGORY_AUTOMATION_ENABLE  = 'contentor_options/automation/category_import';
    const XML_PATH_CATEGORY_SOURCE_LOCALE      = 'contentor_options/source/category_sourcelocale';
    const XML_PATH_CATEGORY_TARGET_STORE_VIEWS = 'contentor_options/targets/category_targetviews';
    const XML_PATH_CATEGORY_FIELDS             = 'contentor_options/fieldDetails/categoryFieldDetails';
    const XML_PATH_CATEGORY_CONTENT_CREATION_FIELDS   = 'contentor_options/contentCreation/categoryFieldDetails';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigurationService constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getVersion() {
        // TODO: Maybe move this into the config file or pull it from the runtime somehow
        return '0.4.0';
    }

    /**
     * Returns token from config
     *
     * @param string|null $store
     * @return string
     */
    public function getToken($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TOKEN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Automation enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isAutomationEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_AUTOMATION_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns category automation enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isCategoryAutomationEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CATEGORY_AUTOMATION_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns versioning enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isVersioningEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_VERSIONING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return is developer mode enabled flag
     *
     * @param string|null $store
     * @return bool
     */
    public function isDeveloperMode($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DEVELOPER_MODE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }


    /**
     * Returns Contentor API url from magento config
     *
     * @param string|null $store
     * @return string
     */
    public function getApiBaseUrl($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_BASE_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Main locale from magento config
     *
     * @param string|null $store
     * @return string
     */
    public function getMainLocale($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAIN_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Source locale from magento config
     *
     * @param string|null $store
     * @return string
     */
    public function getSourceLocale($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SOURCE_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Category Source locale from magento config
     *
     * @param string|null $store
     * @return string
     */
    public function getCategorySourceLocale($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_SOURCE_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Product target store views from magento config
     *
     * @param string|null $store
     * @return array
     */
    public function getTargetStoreViews($store = null)
    {
        return (array) $this->scopeConfig->getValue(
            self::XML_PATH_TARGET_STORE_VIEWS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Category target store views from magento config
     *
     * @param string|null $store
     * @return array
     */
    public function getCategoryTargetStoreViews($store = null)
    {
        return (array) $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_TARGET_STORE_VIEWS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Limitation of requests sent to Contentor
     *
     * @param string|null $store
     * @return array
     */
    public function getLimitRequests($store = null)
    {
        return (array) $this->scopeConfig->getValue(
            self::XML_LIMITATION_REQUESTS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns Product attributes map from magento config
     *
     * @param string|null $store
     * @return array
     */
    public function getProductFields($store = null)
    {
        $data =  $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($data)) {
            return [];
        }

        $result = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value. Error: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * Returns category attributes map from magento config
     *
     * @param string|null $store
     * @return array
     */
    public function getCategoryFields($store = null)
    {
        $data =  $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($data)) {
            return [];
        }

        $result = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value. Error: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * Returns Product attributes map from magento config for content creation
     *
     * @param string|null $store
     * @return array
     */
    public function getContentCreationProductFields($store = null)
    {
        $data =  $this->scopeConfig->getValue(
            self::XML_PATH_CONTENT_CREATION_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($data)) {
            return [];
        }

        $result = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value. Error: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * Returns category attributes map from magento config for content creation
     *
     * @param string|null $store
     * @return array
     */
    public function getContentCreationCategoryFields($store = null)
    {
        $data =  $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_CONTENT_CREATION_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($data)) {
            return [];
        }

        $result = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value. Error: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * Basic config validation.
     * Check required API fields before make real api request.
     *
     * @param string|null $store
     * @return array
     */
    public function validateConfiguration($store = null)
    {
       $result = [
           'error' => false,
           'messages' => []
       ];
        if (empty($this->getToken($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Token';
        }

        if (empty($this->getApiBaseUrl($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Contentor base API Url';
        }

        if (empty($this->getProductFields($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Product Attribute configuration';
        }

        if (empty($this->getSourceLocale($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Source locale';
        }

        if (empty($this->getTargetStoreViews($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Target store views';
        }

       return $result;
    }

    /**
     * Basic config validation for category
     * Check required API fields before make real api request.
     *
     * @param string|null $store
     * @return array
     */
    public function validateCategoryConfiguration($store = null)
    {
        $result = [
            'error' => false,
            'messages' => []
        ];
        if (empty($this->getToken($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Token';
        }

        if (empty($this->getApiBaseUrl($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Contentor base API Url';
        }

        if (empty($this->getCategoryFields($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Category Attribute configuration';
        }

        if (empty($this->getCategorySourceLocale($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Category Source locale';
        }

        if (empty($this->getCategoryTargetStoreViews($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Category Target store views';
        }

        return $result;
    }

    /**
     * Basic config validation.
     * Check required API fields before make real api request.
     *
     * @param string|null $store
     * @return array
     */
    public function validateContentCreationConfiguration($store = null)
    {
        $result = [
            'error' => false,
            'messages' => []
        ];
        if (empty($this->getToken($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Token';
        }

        if (empty($this->getApiBaseUrl($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Contentor base API Url';
        }

        if (empty($this->getContentCreationProductFields($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Product Attribute configuration - content creation';
        }

        if (empty($this->getSourceLocale($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Source locale - content creation';
        }

        if (empty($this->getTargetStoreViews($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Target store views - content creation';
        }

        return $result;
    }

    /**
     * Basic config validation for category content creation
     * Check required API fields before make real api request.
     *
     * @param string|null $store
     * @return array
     */
    public function validateCategoryContentCreationConfiguration($store = null)
    {
        $result = [
            'error' => false,
            'messages' => []
        ];
        if (empty($this->getToken($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Token';
        }

        if (empty($this->getApiBaseUrl($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Contentor base API Url';
        }

        if (empty($this->getContentCreationCategoryFields($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Category Attribute configuration - content creation';
        }

        if (empty($this->getCategorySourceLocale($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor]  Empty Category Source locale - content creation';
        }

        if (empty($this->getCategoryTargetStoreViews($store))) {
            $result['error'] = true;
            $result['messages'][] = '[Contentor] Empty Category Target store views - content creation';
        }

        return $result;
    }
}
