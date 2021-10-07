<?php

namespace Contentor\LocalizationApi\Model\ResourceModel\Category;

use Contentor\LocalizationApi\Model\ResourceModel\Category;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(
            \Contentor\LocalizationApi\Model\Category::class,
            Category::class
        );
    }
}
