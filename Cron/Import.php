<?php

namespace Contentor\LocalizationApi\Cron;

use Contentor\LocalizationApi\Model\Logger\Logger;
use Contentor\LocalizationApi\Model\Service\ProcessUpdates;
use Contentor\LocalizationApi\Service\ConfigurationService;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

class Import
{
    /**
     * @var ProcessUpdates
     */
    private $processUpdates;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * Import constructor.
     * @param Logger $logger
     * @param ResourceConnection $resource
     * @param ProcessUpdates $processUpdates
     * @param ConfigurationService $configurationService
     */
    public function __construct(
        Logger             $logger,
        ResourceConnection $resource,
        ProcessUpdates     $processUpdates,
        ConfigurationService $configurationService
    ) {
        $this->processUpdates = $processUpdates;
        $this->logger = $logger;
        $this->resource = $resource;
        $this->configurationService = $configurationService;
    }

    /**
     * @return $this|bool
     * @throws LocalizedException
     */
    public function execute()
    {
        if ($this->configurationService->isModuleEnabled() === false) {
            return $this;
        }
        
        $connection = $this->resource->getConnection('core_read');

        $lastRun = $this->get($connection, 'lastRun');
        $lastStateChange = $this->get($connection, 'lastStateChange');

        if (empty($lastStateChange)) {
            $this->logger->info('Running initial content synchronization');
        } else {
            $this->logger->info('Running content synchronization, last state change seen ' . $lastStateChange . ' (at ' . $lastRun . ')');
        }

        while (true) {
            $result = $this->processUpdates->execute(
                empty($lastStateChange) ? gmdate('c', strtotime('-2 days')) : $lastStateChange
            );

            // Save the new last state change and then continue
            $this->update($connection, 'lastStateChange', $result['lastStateChange'], $lastStateChange);
            $lastStateChange = $result['lastStateChange'];

            if ($result['updates'] == 0) {
                break;
            }
        }

        $this->update($connection, 'lastRun', gmdate('c'), $lastRun);

        return $this;
    }

    /**
     * @param $connection
     * @param $key
     * @return null
     */
    private function get($connection, $key)
    {
        $table = $this->resource->getTableName('contentor_config');
        $query = $connection->select()
            ->from(['cc' => $table], ['value'])
            ->where('cc.key =:key');
        $binds = [
            'key' => $key
        ];
        $date = $connection->fetchOne($query, $binds);
        if (empty($date)) {
            return null;
        } else {
            return $date;
        }
    }

    /**
     * @param $connection
     * @param $key
     * @param $value
     * @param $previous
     */
    private function update($connection, $key, $value, $previous)
    {
        $table = $this->resource->getTableName('contentor_config');
        if (empty($previous)) {
            $connection->insert($table, ['key' => $key, 'value' => $value]);
        } else {
            $connection->update($table, ['value' => $value], ['`key` = ?' => $key]);
        }
    }
}
