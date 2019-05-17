<?php
namespace Contentor\LocalizationApi\Model\ResourceModel;

use Contentor\LocalizationApi\Api\Data\StatusInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Status
 * @package Contentor\LocalizationApi\Model\ResourceModel
 */
class Status extends AbstractDb
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
            'contentor_status',
            StatusInterface::CONTENTOR_ID
        );
    }
}
