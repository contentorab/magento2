<?php
namespace Contentor\LocalizationApi\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
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

        $setup->endSetup();
    }
}
