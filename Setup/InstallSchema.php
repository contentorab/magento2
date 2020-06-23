<?php
namespace Contentor\LocalizationApi\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Add config table
        $configTable = $setup->getConnection()->newTable(
            $setup->getTable('contentor_config')
        )->addColumn(
            'key',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            16,
            [],
            'Key'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Value'
        )->setComment(
            'Config Table'
        );
        $setup->getConnection()->createTable($configTable);


        // Add status table
        $statusTable = $setup->getConnection()->newTable(
            $setup->getTable('contentor_status')
        )->addColumn(
            'contentor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            [],
            'Contentor ID'
        )->addColumn(
            'status_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            0,
            [],
            'Status Time'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            [],
            'Status'
        )->setComment(
            'Status Table'
        );
        $setup->getConnection()->createTable($statusTable);

        $typeTable = $setup->getConnection()->newTable(
            $setup->getTable('contentor_type')
        )->addColumn(
            'contentor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            [],
            'Contentor ID'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            16,
            [],
            'Type'
        )->setComment(
            'Type Table'
        );
        $setup->getConnection()->createTable($typeTable);

        // Add status table
        $productTable = $setup->getConnection()->newTable(
            $setup->getTable('contentor_products')
        )->addColumn(
            'contentor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            [],
            'Contentor ID'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Product SKU'
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
        )->setComment(
            'Type Table'
        );
        $setup->getConnection()->createTable($productTable);
        $setup->getConnection()->insert($setup->getTable('core_config_data'),[
            'path' =>       'contentor_options/fieldDetails/productFieldDetails',
            'value' =>      '{"_1592900795333_333":{"attribute":"productURL","store":"1","type":"context","data":"string"},"_1592900803680_680":{"attribute":"name","store":"1","type":"localizable","data":"string"},"_1592900817518_518":{"attribute":"description","store":"1","type":"localizable","data":"html:relaxed"},"_1592900823176_176":{"attribute":"short_description","store":"1","type":"localizable","data":"string"}}',
        ]);

        $setup->getConnection()->insert($setup->getTable('core_config_data'),[
            'path' =>       'contentor_options/fieldDetails/categoryFieldDetails',
            'value' =>      '{"_1592903609509_509":{"attribute":"url_key","store":"1","type":"context","data":"string"},"_1592903616327_327":{"attribute":"name","store":"1","type":"localizable","data":"string"},"_1592903621941_941":{"attribute":"meta_title","store":"1","type":"localizable","data":"string"},"_1592903629072_72":{"attribute":"description","store":"1","type":"localizable","data":"html:relaxed"}}'
        ]);

        $setup->endSetup();
    }
}
