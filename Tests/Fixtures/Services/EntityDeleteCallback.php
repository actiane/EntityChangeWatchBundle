<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

/**
 * Class EntityDeleteCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class EntityDeleteCallback
{
    public $testDeleteAccess = false;

    public $testDeleteAfterAccess = false;

    public function testDelete()
    {
        $this->testDeleteAccess = true;
    }

    public function testDeleteAfter()
    {
        $this->testDeleteAfterAccess = true;
    }

    public function reset(){
        $this->testDeleteAccess = false;
        $this->testDeleteAfterAccess = false;
    }
}
