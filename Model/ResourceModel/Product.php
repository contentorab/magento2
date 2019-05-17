<?php
namespace Contentor\LocalizationApi\Model\ResourceModel;

use Contentor\LocalizationApi\Api\Data\ProductInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Product
 * @package Contentor\LocalizationApi\Model\ResourceModel
 */
class Product extends AbstractDb
{
    /**
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @inheritdoc
     * @void
     */
    public function _construct()
    {
        $this->_init(
            'contentor_products',
            ProductInterface::CONTENTOR_ID
        );
    }
}
