<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntityCreateCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class EntityCreateCallback
{
    public $testCreateAccess = false;

    public $testCreateAfterAccess = false;

    public function testCreate(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testCreateAccess = true;
    }

    public function testCreateAfter(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testCreateAfterAccess = true;
    }

    public function reset(){
        $this->testCreateAccess = false;
        $this->testCreateAfterAccess = false;
    }
}
