<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Model\ResourceModel\Product\Import\Report\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Contentor\LocalizationApi\Model\ResourceModel\Product\Import\Report\Collection as ReportCollection;
use Contentor\LocalizationApi\Model\ResourceModel\Product\Import\Report as ReportResource;

/**
 * Grid Collection for Product Import Reports
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collection extends ReportCollection implements SearchResultInterface
{
    private AggregationInterface $aggregations;

    protected function _construct()
    {
        $this->_init(Document::class, ReportResource::class);
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    public function getSearchCriteria()
    {
        return null;
    }

    public function setSearchCriteria(?SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    public function getTotalCount()
    {
        return $this->getSize();
    }

    public function setTotalCount($totalCount)
    {
        return $this;
    }

    public function setItems(array $items = null)
    {
        return $this;
    }
}
