<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

/**
 * Class SubEntityCreateCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class SubEntityCreateCallback
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
