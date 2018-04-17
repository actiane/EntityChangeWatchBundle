<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntityDeleteCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class EntityDeleteCallback
{
    public $testDeleteAccess = false;

    public $testDeleteAfterAccess = false;

    public function testDelete(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testDeleteAccess = true;
    }

    public function testDeleteAfter(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testDeleteAfterAccess = true;
    }

    public function reset(){
        $this->testDeleteAccess = false;
        $this->testDeleteAfterAccess = false;
    }
}
