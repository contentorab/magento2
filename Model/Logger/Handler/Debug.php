<?php
namespace Contentor\LocalizationApi\Model\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Debug
 * @package Contentor\LocalizationApi\Model\Logger\Handler
 *
 * Main contentor logger handler
 */
class Debug extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/contentor_api.log';
}
