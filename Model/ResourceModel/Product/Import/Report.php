<?php declare(strict_types=1);

namespace Contentor\LocalizationApi\Model\ResourceModel\Product\Import;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Report extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('contentor_product_import_report', 'id');
    }
}
