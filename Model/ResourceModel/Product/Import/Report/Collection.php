<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Model\ResourceModel\Product\Import\Report;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Contentor\LocalizationApi\Model\Product\Import\Report as Model;
use Contentor\LocalizationApi\Model\ResourceModel\Product\Import\Report as Resource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            Model::class,
            Resource::class
        );
    }
}
