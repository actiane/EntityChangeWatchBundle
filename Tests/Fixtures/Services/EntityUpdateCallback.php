<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

/**
 * Class EntityUpdateCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class EntityUpdateCallback
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
