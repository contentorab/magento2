<?php
namespace Contentor\LocalizationApi\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AddDefaultConfig implements DataPatchInterface, PatchRevertableInterface
{
    private const CATEGORIES_CONFIG_PATH = 'contentor_options/fieldDetails/categoryFieldDetails';
    private const PRODUCTS_CONFIG_PATH = 'contentor_options/fieldDetails/productFieldDetails';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ScopeConfigInterface $scopeConfig
    ) {
        /**
         * If before, we pass $setup as argument in install/upgrade function, from now we start
         * inject it with DI. If you want to use setup, you can inject it, with the same way as here
         */
        $this->moduleDataSetup = $moduleDataSetup;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $productsConfiguration = $this->scopeConfig->getValue(self::PRODUCTS_CONFIG_PATH);
        if (!$productsConfiguration) {
            $this->moduleDataSetup->getConnection()->insert($this->moduleDataSetup->getTable('core_config_data'), [
                'path' => self::PRODUCTS_CONFIG_PATH,
                'value' => '{"_1592900795333_333":{"attribute":"productURL","store":"1","type":"context","data":"string"},"_1592900803680_680":{"attribute":"name","store":"1","type":"localizable","data":"string"},"_1592900817518_518":{"attribute":"description","store":"1","type":"localizable","data":"html:relaxed"},"_1592900823176_176":{"attribute":"short_description","store":"1","type":"localizable","data":"string"}}',
            ]);
        }

        $categoriseConfiguration = $this->scopeConfig->getValue(self::CATEGORIES_CONFIG_PATH);
        if (!$categoriseConfiguration) {
            $this->moduleDataSetup->getConnection()->insert($this->moduleDataSetup->getTable('core_config_data'), [
                'path' => self::CATEGORIES_CONFIG_PATH,
                'value' => '{"_1592903609509_509":{"attribute":"url_key","store":"1","type":"context","data":"string"},"_1592903616327_327":{"attribute":"name","store":"1","type":"localizable","data":"string"},"_1592903621941_941":{"attribute":"meta_title","store":"1","type":"localizable","data":"string"},"_1592903629072_72":{"attribute":"description","store":"1","type":"localizable","data":"html:relaxed"}}'
            ]);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    public function revert()
    {

        $this->moduleDataSetup->getConnection()->startSetup();
        $this->moduleDataSetup->getConnection()->delete(
            $this->moduleDataSetup->getTable('core_config_data'),
            'path = "' . self::PRODUCTS_CONFIG_PATH . '"'
        );
        $this->moduleDataSetup->getConnection()->delete(
            $this->moduleDataSetup->getTable('core_config_data'),
            'path = "' . self::CATEGORIES_CONFIG_PATH . '"'
        );
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
