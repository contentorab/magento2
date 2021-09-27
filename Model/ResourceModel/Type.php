<?php

namespace Contentor\LocalizationApi\Model\ResourceModel;

use Contentor\LocalizationApi\Api\Data\TypeInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Model\ResourceModel\Db\Context;
use Psr\Log\LoggerInterface;

class Type extends AbstractDb
{
    /**
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        $connectionName = null
    ) {
        $this->logger = $logger;
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
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
     */
    public function getByContentorId($contentorId): string
    {
        $result = [];
        try {
            $connection = $this->getConnection();
            $select = $connection->select();
            $select->from($this->getMainTable(), ['type']);
            $select->where('contentor_id =?', $contentorId);
            $result = $connection->fetchOne($select);
        } catch (\Exception $e) {
            $this->logger->error('Cannot get contentor_id: ' . $e->getMessage());
        }
        return $result;
    }
}
