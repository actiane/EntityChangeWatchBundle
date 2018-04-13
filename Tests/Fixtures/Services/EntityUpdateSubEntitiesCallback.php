<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

/**
 * Class EntityUpdatSubEntitiesCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class EntityUpdateSubEntitiesCallback
{
    public $testUpdateSubEntitiesAccess = false;

    public $testUpdateSubEntitiesAfterAccess = false;

    public function testUpdateSubEntities()
    {
        $this->testUpdateSubEntitiesAccess = true;
    }

    public function testUpdateSubEntitiesAfter()
    {
        $this->testUpdateSubEntitiesAfterAccess = true;
    }

    public function reset(){
        $this->testUpdateSubEntitiesAccess = false;
        $this->testUpdateSubEntitiesAfterAccess = false;
    }
}
