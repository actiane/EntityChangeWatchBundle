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

    public $testUpdateSubEntitiesAfterAccess = false;

    public function testUpdateSubEntities(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateSubEntitiesAccess = true;
    }

    public function testUpdateSubEntitiesAfter(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateSubEntitiesAfterAccess = true;
    }

    public function reset(){
        $this->testUpdateSubEntitiesAccess = false;
        $this->testUpdateSubEntitiesAfterAccess = false;
    }
}
