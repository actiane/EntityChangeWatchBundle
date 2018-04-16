<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

/**
 * Class SubEntityUpdateCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class SubEntityUpdateCallback
{
    public $testUpdateAccess = false;

    public $testUpdateAfterAccess = false;

    public function testUpdate()
    {
        $this->testUpdateAccess = true;
    }

    public function testUpdateAfter()
    {
        $this->testUpdateAfterAccess = true;
    }

    public function reset(){
        $this->testUpdateAccess = false;
        $this->testUpdateAfterAccess = false;
    }
}
