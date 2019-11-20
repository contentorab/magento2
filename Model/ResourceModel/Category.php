<?php
namespace Contentor\LocalizationApi\Model\ResourceModel;

use Contentor\LocalizationApi\Api\Data\CategoryInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Category
 * @package Contentor\LocalizationApi\Model\ResourceModel
 */
class Category extends AbstractDb
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
            'contentor_category',
            CategoryInterface::CONTENTOR_ID
        );
    }
}
