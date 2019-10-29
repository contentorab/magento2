<?php
namespace Contentor\LocalizationApi\Model\ResourceModel\Category;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Contentor\LocalizationApi\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(
            \Contentor\LocalizationApi\Model\Category::class,
            \Contentor\LocalizationApi\Model\ResourceModel\Category::class
        );
    }
}
