<?php

namespace Contentor\LocalizationApi\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Psr\Log\LoggerInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UpgradeSchema constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();
        try {
            if (version_compare($context->getVersion(), '0.3.0') < 0) {
                // Add indexes to the status table
                $setup->getConnection()->addColumn(
                    $setup->getTable('contentor_status'),
                    'id',
                    [
                        'type' => Table::TYPE_INTEGER,
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
                    AdapterInterface::INDEX_TYPE_PRIMARY
                );

                // Merged index for versioning
                $setup->getConnection()->addIndex(
                    $setup->getTable('contentor_products'),
                    'id_and_locales',
                    ['sku', 'source_locale', 'target_locale']
                );

                // Migrate the identifier fields to 36 characters to align with API specification
                $setup->getConnection()->modifyColumn(
                    $setup->getTable('contentor_status'),
                    'contentor_id',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 36
                    ]
                );

                $setup->getConnection()->modifyColumn(
                    $setup->getTable('contentor_type'),
                    'contentor_id',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 36,
                        'nullable' => false,
                        'primary' => true,
                    ]
                );

                $setup->getConnection()->modifyColumn(
                    $setup->getTable('contentor_products'),
                    'contentor_id',
                    [
                        'type' => Table::TYPE_TEXT,
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
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => false,
                        'default' => 0,
                        'comment' => 'Synchronize Type'
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
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => 36,
                        'comment' => 'Delivery Speed'
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

            /**
             *  Added product_id field to sync products table
             */
            if (version_compare($context->getVersion(), '0.7.0') < 0) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('contentor_products'),
                    'm2_product_id',
                    [
                        'type' => Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'M2 Product Id',
                        'unsigned' => true,
                        'default' => 0
                    ]
                );

                $setup->getConnection()->addIndex(
                    $setup->getTable('contentor_products'),
                    'on_m2_product_id',
                    'm2_product_id'
                );
            }

            /**
             *  Added attribution field to sync products table
             */
            if (version_compare($context->getVersion(), '0.7.3') < 0) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('contentor_products'),
                    'machine_translation',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Machine Translation',
                    ]
                );
                $setup->getConnection()->addColumn(
                    $setup->getTable('contentor_products'),
                    'attribution',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Attribution for machine-translation',
                    ]
                );
            }
            /**
             *  Added attribution field to sync category table
             */
            if (version_compare($context->getVersion(), '0.7.3') < 0) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('contentor_category'),
                    'machine_translation',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Machine Translation',
                    ]
                );
                $setup->getConnection()->addColumn(
                    $setup->getTable('contentor_category'),
                    'attribution',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Attribution for machine-translation',
                    ]
                );
            }

        } catch (\Exception $e) {
            $this->logger->error('Cannot update tables: ' . $e->getMessage());
        }
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return UpgradeSchema
     */
    private function _createCategoryContentorProducts(SchemaSetupInterface $setup): UpgradeSchema
    {
        try {
            $syncCategoryTable = $setup->getConnection()->newTable(
                $setup->getTable('contentor_category')
            )->addColumn(
                'contentor_id',
                Table::TYPE_TEXT,
                36,
                [],
                'Contentor ID'
            )->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                10,
                [],
                'Category Id'
            )->addColumn(
                'source_locale',
                Table::TYPE_TEXT,
                16,
                [],
                'Source Locale'
            )->addColumn(
                'target_locale',
                Table::TYPE_TEXT,
                16,
                [],
                'Target Locale'
            )->addColumn(
                'target_store',
                Table::TYPE_INTEGER,
                5,
                [],
                'Target Store'
            )->addColumn(
                'sent_time',
                Table::TYPE_DATETIME,
                0,
                [],
                'Sent Time'
            )->addColumn(
                'completed_time',
                Table::TYPE_DATETIME,
                0,
                [],
                'Completed Time'
            )->addColumn(
                'deadline_time',
                Table::TYPE_DATETIME,
                0,
                [],
                'Deadline Time'
            )->addColumn(
                'canceled_time',
                Table::TYPE_DATETIME,
                0,
                [],
                'Canceled Time'
            )->addColumn(
                'state',
                Table::TYPE_TEXT,
                16,
                [],
                'State'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                16,
                [],
                'Type'
            )->addColumn(
                'synchronize_type',
                Table::TYPE_INTEGER,
                10,
                [
                    'default' => 0
                ],
                'Synchronize Type'
            )
                ->addColumn(
                    'delivery_speed',
                    Table::TYPE_TEXT,
                    36,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Delivery Speed'
                )
                ->addColumn(
                    'machine_translation',
                    Table::TYPE_TEXT,
                    36,
                    [
                        'nullable' => true,
                        'default' => null,
                    ],
                    'Machine Translation'
                )
                ->addColumn(
                    'attribution',
                    Table::TYPE_TEXT,
                    36,
                    [
                        'nullable' => true,
                    ],
                    'Attribution for machine-translation'
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
        } catch (\Exception $e) {
            $this->logger->error('Cannot create table: ' . $e->getMessage());
        }
        return $this;
    }
}
