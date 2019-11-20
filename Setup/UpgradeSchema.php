<?php
namespace Contentor\LocalizationApi\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Contentor\LocalizationApi\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.3.0') < 0) {
             // Add indexes to the status table
             $setup->getConnection()->addColumn(
                $setup->getTable('contentor_status'),
                'id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'comment' => 'Id of status message'
                ]
            );

            $setup->getConnection()->addIndex(
                $setup->getTable('contentor_status'),
                'on_id',
                'contentor_id'
            );

            // Index for config table
            $setup->getConnection()->addIndex(
                $setup->getTable('contentor_config'),
                'primary',
                'key',
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY
            );

            // Merged index for versioning
            $setup->getConnection()->addIndex(
                $setup->getTable('contentor_products'),
                'id_and_locales',
                array('sku', 'source_locale', 'target_locale')
            );

             // Migrate the identifier fields to 36 characters to align with API specification
             $setup->getConnection()->modifyColumn(
                $setup->getTable('contentor_status'),
                'contentor_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 36
                ]
            );

            $setup->getConnection()->modifyColumn(
                $setup->getTable('contentor_type'),
                'contentor_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 36,
                    'nullable' => false,
                    'primary' => true,
                ]
            );

            $setup->getConnection()->modifyColumn(
                $setup->getTable('contentor_products'),
                'contentor_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 36,
                    'nullable' => false,
                    'primary' => true,
                ]
            );
        }

        /**
         *  Added column to check if the records is localization or content creation
         *  Default is localization synchronize
         */
        if (version_compare($context->getVersion(), '0.4.0') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('contentor_products'),
                'synchronize_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default'  => 0,
                    'comment'  => 'Synchronize Type'
                ]
            );

            $setup->getConnection()->addIndex(
                $setup->getTable('contentor_products'),
                'on_synchronize_type',
                'synchronize_type'
            );
        }

        /**
         *  Added column to show delivery-speed
         */
        if (version_compare($context->getVersion(), '0.5.0') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('contentor_products'),
                'delivery_speed',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => 36,
                    'comment'  => 'Delivery Speed'
                ]
            );

            $setup->getConnection()->addIndex(
                $setup->getTable('contentor_products'),
                'on_delivery_speed',
                'delivery_speed'
            );
        }

        /**
         *  Create category contentor synchronize table
         */
        if (version_compare($context->getVersion(), '0.6.0') < 0) {
            $this->_createCategoryContentorProducts($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function _createCategoryContentorProducts(SchemaSetupInterface $setup) {

        $syncCategoryTable = $setup->getConnection()->newTable(
            $setup->getTable('contentor_category')
        )->addColumn(
            'contentor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            36,
            [],
            'Contentor ID'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            [],
            'Category Id'
        )->addColumn(
            'source_locale',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            16,
            [],
            'Source Locale'
        )->addColumn(
            'target_locale',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            16,
            [],
            'Target Locale'
        )->addColumn(
            'target_store',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            5,
            [],
            'Target Store'
        )->addColumn(
            'sent_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            0,
            [],
            'Sent Time'
        )->addColumn(
            'completed_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            0,
            [],
            'Completed Time'
        )->addColumn(
            'deadline_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            0,
            [],
            'Deadline Time'
        )->addColumn(
            'canceled_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            0,
            [],
            'Canceled Time'
        )->addColumn(
            'state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            16,
            [],
            'State'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            16,
            [],
            'Type'
        )->addColumn(
            'synchronize_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            [
                'default' => 0
            ],
            'Synchronize Type'
        )
        ->addColumn(
            'delivery_speed',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            36,
            [
                'nullable' => true,
                'default' => null,
            ],
            'Delivery Speed'
        )
        ->addIndex(
            $setup->getIdxName('contentor_category', ['synchronize_type']),
            ['synchronize_type']
        )
        ->addIndex(
            $setup->getIdxName('contentor_category', ['delivery_speed']),
            ['delivery_speed']
        )
        ->addIndex(
            $setup->getIdxName('contentor_category', ['category_id']),
            ['category_id']
        )
        ->setComment(
            'Sync Category Contentor Table'
        );
        $setup->getConnection()->createTable($syncCategoryTable);
        return $this;
    }
}
