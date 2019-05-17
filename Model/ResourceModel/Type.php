<?php
namespace Contentor\LocalizationApi\Model\ResourceModel;

use Contentor\LocalizationApi\Api\Data\TypeInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Type
 * @package Contentor\LocalizationApi\Model\ResourceModel
 */
class Type extends AbstractDb
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
            'contentor_type',
            TypeInterface::CONTENTOR_ID
        );
    }

    /**
     * @param int $contentorId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByContentorId($contentorId)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from($this->getMainTable(), ['type']);
        $select->where('contentor_id =?', $contentorId);
        return $connection->fetchOne($select);
    }
}
