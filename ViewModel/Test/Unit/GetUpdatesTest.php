<?php

namespace Contentor\LocalizationApi\Test\Unit;

use Magento\Framework\App\Bootstrap;
use Contentor\LocalizationApi\Model\Gateway\GetUpdates;

class GetUpdatesTest extends \PHPUnit\Framework\TestCase {
    
    protected $getUpdates;

    protected function setUp(): void {

        require __DIR__ . '/../../../../../app/bootstrap.php';

        $bootstrap = Bootstrap::create(BP, $_SERVER);
        $obj = $bootstrap->getObjectManager();
        $this->getUpdates = $obj->create(GetUpdates::class);

    }

    public function tearDown(): void {

        /* Perform action after the test run */

    }

    public function testGetUpdates() {
        // get updates from last 2 minutes
        $date = date('Y-m-d H:i:s', strtotime('-2 minutes'));
        $result = $this->getUpdates->execute($date);
        
        $result_data = $result['data'];       
        $expected_data = [];

        $result_pagination = $result['pagination'];
        $expected_pagination = [
            'page'=> 1,
            'pages' => 0,
            'total' => 0
        ];

        $this->assertEquals($result_data, $expected_data );
        $this->assertEquals($result_pagination, $expected_pagination );
    }
}