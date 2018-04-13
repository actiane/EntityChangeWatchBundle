<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

/**
 * Class EntityCreateCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class EntityCreateCallback
{
    public $testCreateAccess = false;

    public $testCreateAfterAccess = false;

    public function testCreate()
    {
        $this->testCreateAccess = true;
    }

    public function testCreateAfter()
    {
        $this->testCreateAfterAccess = true;
    }

    public function reset(){
        $this->testCreateAccess = false;
        $this->testCreateAfterAccess = false;
    }
}
