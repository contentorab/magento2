<?php
namespace Contentor\LocalizationApi\Cron;

use Contentor\LocalizationApi\Model\Service\ProcessUpdates;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Import
 * @package Contentor\LocalizationApi\Cron
 */
class Import
{
    /**
     * @var ProcessUpdates
     */
    private $processUpdates;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Import constructor.
     * @param LoggerInterface $logger
     * @param ResourceConnection $resource
     * @param ProcessUpdates $processUpdates
     */
    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resource,
        ProcessUpdates $processUpdates
    ) {
        $this->processUpdates = $processUpdates;
        $this->logger = $logger;
        $this->resource = $resource;
    }

    /**
     * @return $this|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
            $now = gmdate('Y-m-d H:i');
            $connection = $this->resource->getConnection('core_read');
            $table = $this->resource->getTableName('contentor_config');
            $query = "SELECT `value` FROM `" . $table . "` WHERE `key` = 'lastRun'";
            $lastRun = $connection->fetchOne($query);

            $noTime = false;
            if (!$lastRun) {
                $noTime = true;
                $lastRun = gmdate('Y-m-d H:i', strtotime('-2 days'));
            }

            $result = false;
            $result = $this->processUpdates->execute($lastRun);

            if ($result !== true) {
                $this->logger->debug('Error cron job');
            } else {
                // If successful, write new time to db!
                $connection = $this->resource->getConnection('core_write');
                $table = $this->resource->getTableName('contentor_config');

                if ($noTime) {
                    $query = "INSERT INTO `" . $table . "` (`key`, `value`) VALUES ('lastRun', '" . $now . "')";
                } else {
                    $query = "UPDATE `" . $table . "` SET `value` = '" . $now . "' WHERE `key` = 'lastRun'";
                }
                $connection->query($query);
                return true;
            }
        return $this;
    }
}
