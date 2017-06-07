<?php
namespace Contentor\LocaliszationApi\Setup;
 
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
            \Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
            16,
            [],
            'Key'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
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
			\Magento\Framework\DB\Ddl\Table::TYPE_CHAR,
			10,
			[],
			'Contentor ID'
		)->addColumn(
			'stauts_time',
			\Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
			0,
			[],
			'Status Time'
		)->addColumn(
			'stauts',
			\Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
			128,
			[],
			'Status'
		)->setComment(
			'Status Table'
    	);
    	$setup->getConnection()->createTable($statusTable);
        
    	$typeTable = $setup->getConnection()->newTable(
    		$setup->getTable('contentor_status')
    	)->addColumn(
    		'contentor_id',
    		\Magento\Framework\DB\Ddl\Table::TYPE_CHAR,
    		10,
    		[],
    		'Contentor ID'
    	)->addColumn(
    		'type',
    		\Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
    		16,
    		[],
    		'Type'
    	)->setComment(
    		'Status Table'
    	);
    	$setup->getConnection()->createTable($typeTable);
    	
    	// Add status table
    	$productTable = $setup->getConnection()->newTable(
			$setup->getTable('contentor_products')
		)->addColumn(
			'contentor_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_CHAR,
			10,
			[],
			'Contentor ID'
		)->addColumn(
    		'sku',
    		\Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
    		32,
    		[],
    		'Product SKU'
		)->addColumn(
    		'source_locale',
    		\Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
    		16,
    		[],
    		'Source Locale'
		)->addColumn(
    		'target_locale',
    		\Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
    		16,
    		[],
    		'Target Locale'
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
    		\Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
    		16,
    		[],
    		'State'
		)->addColumn(
    		'type',
    		\Magento\Framework\DB\Ddl\Table::TYPE_VARCHAR,
    		16,
    		[],
    		'Type'
		)->setComment(
    		'Type Table'
    	);
    	$setup->getConnection()->createTable($productTable);
    	
        		
        $setup->endSetup();
    }
}