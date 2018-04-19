<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntityUpdatSubEntitiesCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class EntityUpdateSubEntitiesCallback
{
    public $testUpdateSubEntitiesAccess = false;

    public function testUpdateSubEntities(Entity $entity, $changedProperties)
    {
        $this->testUpdateSubEntitiesAccess = true;
    }

    public function reset(){
        $this->testUpdateSubEntitiesAccess = false;
    }
}
