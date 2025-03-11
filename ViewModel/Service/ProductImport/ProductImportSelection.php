<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Service\ProductImport;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Config;
use Magento\Catalog\Api\Data\ProductInterface;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Contentor\LocalizationApi\Model\Product\Import\Report;
use Contentor\LocalizationApi\Model\ResourceModel\Product\Import\Report\CollectionFactory as ReportCollectionFactory;

/**
 * Service class for selecting products to be imported
 */
class ProductImportSelection
{
    private ConfigurationService $contentorConfig;

    private Config $eavConfig;

    private MetadataPool $metadataPool;

    private ProductCollectionFactory $productCollectionFactory;

    private ReportCollectionFactory $reportCollectionFactory;

    private string $targetStoreViews = '';

    public function __construct(
        ConfigurationService $contentorConfig,
        Config $eavConfig,
        MetadataPool $metadataPool,
        ProductCollectionFactory $productCollectionFactory,
        ReportCollectionFactory $reportCollectionFactory
    ) {
        $this->contentorConfig = $contentorConfig;
        $this->eavConfig = $eavConfig;
        $this->metadataPool = $metadataPool;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->reportCollectionFactory = $reportCollectionFactory;
    }

    /**
     * Selects products to be synced:
     * - Products that have attribute values in the target store views for any of the configured attributes
     * - And have no contentor_products entry
     *
     * @return Product[]
     */
    public function select(): array
    {
        $this->targetStoreViews = implode(',', $this->contentorConfig->getTargetStoreViews() ?? []);
        $allAttributes = $this->getProductAttributes();
        $contentorConfiguredAttributes = $this->contentorConfig->getProductFields();
        $attributeCodes = array_column($contentorConfiguredAttributes, 'attribute');

        // Organize attribute IDs by backed table
        $valueTables = [];
        foreach ($attributeCodes as $attributeCode) {
            if ($attributeCode === 'productURL') {
                continue;
            }

            $table = $allAttributes[$attributeCode]->getBackendTable();
            $valueTables[$table][] = $allAttributes[$attributeCode]->getAttributeId();
        }

        $productCollection = $this->productCollectionFactory->create();

        // Select product ids that have attribute values in the target store views
        $select = $productCollection->getSelect();

        $orWhere = '';
        $firstTable = true;
        foreach ($valueTables as $table => $ids) {
            $select->joinLeft(
                $table,
                $this->backendTableJoinCondition($table, $ids),
                []
            );

            $orWhereAddition = $table . '.value IS NOT NULL';
            if (!$firstTable) {
                $orWhereAddition = ' OR ' . $orWhereAddition;
            }
            $orWhere .= $orWhereAddition;
            $firstTable = false;
        }
        $select->where($orWhere);

        // Select product ids that have no contentor_products entry
        $identityField = $this->metadataPool->getMetadata(ProductInterface::class)->getIdentifierField();
        $select->joinLeft(
            'contentor_products',
            'e.' . $identityField . ' = contentor_products.m2_product_id',
            ['contentor_products.m2_product_id']
        );

        // And have no contentor_product_import_report entry with status other than 0 (pending)
        $select->joinLeft(
            'contentor_product_import_report',
            $this->reportTableJoinCondition(),
            ['contentor_product_import_report.product_id']
        );

        $select
            ->where('contentor_products.m2_product_id IS NULL')
            ->where('contentor_product_import_report.product_id IS NULL')
            ->group('e.' . $identityField)
        ;

        $productCollection->loadWithFilter();
        return $productCollection->getItems();
    }

    /**
     * Selects products to be retried
     *
     * @return Report[]
     */
    public function selectForRetry(): array
    {
        $reportCollection = $this->reportCollectionFactory->create();
        $reportCollection->addFieldToFilter('status', ['in' => [Report::STATUS_ERROR]]);
        $identityField = $this->metadataPool->getMetadata(ProductInterface::class)->getIdentifierField();
        $reportCollection->join(
            'catalog_product_entity',
            'main_table.product_id = catalog_product_entity.' . $identityField,
            'sku'
        );
        return $reportCollection->getItems();
    }

    /**
     * Get attributes for product entity type
     *
     * @return AbstractAttribute[]
     * @throws \Exception
     */
    private function getProductAttributes(): array
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $eavEntityType = $metadata->getEavEntityType();
        return null === $eavEntityType ? [] : $this->eavConfig->getEntityAttributes($eavEntityType);
    }

    /**
     * Generates join condition for attribute backend value table
     * Finding values for configured attributes in the target store views
     *
     * @param string $backendTable
     * @param array $ids
     * @return string
     */
    private function backendTableJoinCondition(string $backendTable, array $ids): string
    {
        $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        $parts = [
            $backendTable . '.' . $linkField . ' = e.' . $linkField,
            $backendTable . '.attribute_id IN (' . implode(',', $ids) . ')',
            $backendTable . '.store_id IN (' . $this->targetStoreViews . ')',
        ];
        return implode(' AND ', $parts);
    }

    /**
     * Generates condition for a left join of contentor_product_import_report table
     *  We want to select products that either don't have a contentor_product_import_report entry,
     *  or have an entry where status is 0,
     *
     * @return string
     */
    private function reportTableJoinCondition(): string
    {
        $identityField = $this->metadataPool->getMetadata(ProductInterface::class)->getIdentifierField();
        $parts = [
            0 => 'e.' . $identityField . ' = contentor_product_import_report.product_id',
            1 => sprintf('contentor_product_import_report.status <> %d', Report::STATUS_PENDING),
        ];

        return implode(' AND ', $parts);
    }
}
