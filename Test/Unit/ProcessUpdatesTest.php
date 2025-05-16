<?php

/* Test for Model\Service\ProcessUpdates
It checks if pagination works and if goes troug all the products sent. 
Important: Before the test 30 updates should be sent for localization or content creation in admin.
*/

namespace Contentor\LocalizationApi\Test\Unit;
 
use Magento\Framework\App\Bootstrap;
use \Contentor\LocalizationApi\Cron\Import;
use \Contentor\LocalizationApi\Model\Service\ProcessUpdates;
 
class ProcessUpdatesTest extends \PHPUnit\Framework\TestCase
{

    protected $processUpdates; 
   
    public function setUp(): void
    {
        require __DIR__ . '/../../../../../app/bootstrap.php';

        $bootstrap = Bootstrap::create(BP, $_SERVER);
        $obj = $bootstrap->getObjectManager();
        $this->processUpdates = $obj->create(ProcessUpdates::class);
    }
 
    public function testProcessUpdates()
    {
        // Before test, 30 bulk products should be sent for localization from admin

        // Running process updates from last 5 minutes, and return number of updates processed
        $date = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        $result = $this->processUpdates->execute($date);
        $updates_count = $result['updates'];

        // Should retunr 30 results, in our case
        $expected = 30;
       
        $this->assertEquals($updates_count, $expected );
    }
 
}
