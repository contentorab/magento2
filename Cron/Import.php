<?php
namespace Contentor\LocalizationApi\Cron;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Contentor\LocalizationApi\Helper\ContentorAPI;

class Import
{

    protected $_logger;
    protected $_contentorApi;

    public function __construct(LoggerInterface $logger, ResourceConnection $resource, ContentorAPI $contentorApi)
    {
        $this->_logger = $logger;
        $this->_resource = $resource;
        $this->_contentorApi = $contentorApi;
    }

    public function execute()
    {
        if ($this->_contentorApi->testAuth()) {
            $now = gmdate('Y-m-d H:i');
            $connection = $this->_resource->getConnection('core_read');
            $table = $this->_resource->getTableName('contentor_config');
            $query = "SELECT `value` FROM `" . $table . "` WHERE `key` = 'lastRun'";
            $lastRun = $connection->fetchOne($query);

            $noTime = false;
            if (!$lastRun) {
                $noTime = true;
                $lastRun = gmdate('Y-m-d H:i', strtotime('-2 days'));
            }

            $result = false;
            $result = $this->_contentorApi->receive($lastRun);

            if ($result !== true) {
                $this->_logger->addDebug('Error cron job');
            } else {
                // If successful, write new time to db!
                $connection = $this->_resource->getConnection('core_write');
                $table = $this->_resource->getTableName('contentor_config');

                if ($noTime) {
                    $query = "INSERT INTO `" . $table . "` (`key`, `value`) VALUES ('lastRun', '" . $now . "')";
                } else {
                    $query = "UPDATE `" . $table . "` SET `value` = '" . $now . "' WHERE `key` = 'lastRun'";
                }

                $connection->query($query);

                return true;
            }
        }

        return $this;
    }
}
