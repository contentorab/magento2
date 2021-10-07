<?php

namespace Contentor\LocalizationApi\Model\ResourceModel\Product;

use Contentor\LocalizationApi\Model\ResourceModel\Product;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(
            \Contentor\LocalizationApi\Model\Product::class,
            Product::class
        );
    }
}
