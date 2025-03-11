<?php

namespace Contentor\LocalizationApi\Model\ResourceModel;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Product extends AbstractDb
{
    /**
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(
            'contentor_products',
            ProductInterface::CONTENTOR_ID
        );
    }
}
