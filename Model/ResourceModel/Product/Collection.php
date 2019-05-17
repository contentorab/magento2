<?php
namespace Contentor\LocalizationApi\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Contentor\LocalizationApi\Model\ResourceModel\Product
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(
            \Contentor\LocalizationApi\Model\Product::class,
            \Contentor\LocalizationApi\Model\ResourceModel\Product::class
        );
    }
}
