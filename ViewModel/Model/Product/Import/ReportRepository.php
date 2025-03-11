<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Model\Product\Import;

use Contentor\LocalizationApi\Model\ResourceModel\Product\Import\ReportFactory as ReportResourceFactory;
use Contentor\LocalizationApi\Model\ResourceModel\Product\Import\Report\CollectionFactory;

/**
 * Repository for Product Import Reports
 */
class ReportRepository
{
    private ReportResourceFactory $reportResourceFactory;

    private CollectionFactory $collectionFactory;

    public function __construct(
        ReportResourceFactory $reportResourceFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->reportResourceFactory = $reportResourceFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param Report $report
     * @return void
     */
    public function save(Report $report): void
    {
        $reportResource = $this->reportResourceFactory->create();
        $reportResource->save($report);
    }

    /**
     * @param integer $productId
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return Report
     */
    public function getByProductIdAndLocales(int $productId, string $sourceLocale, string $targetLocale): Report
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('source_locale', $sourceLocale)
            ->addFieldToFilter('target_locale', $targetLocale)
        ;

        return $collection->getFirstItem();
    }

    /**
     * @param integer $productId
     * @param string $sourceLocale
     * @param string $targetLocale
     * @return Report
     */
    public function getLatest(): Report
    {
        $collection = $this->collectionFactory->create();
        $collection->setOrder('created_at', 'DESC');
        return $collection->getFirstItem();
    }
}
