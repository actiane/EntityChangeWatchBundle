<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SubEntityCreateCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class SubEntityCreateCallback
{
    public $testCreateAccess = false;

    public $testCreateAfterAccess = false;

    public function testCreate(SubEntity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testCreateAccess = true;
    }

    public function testCreateAfter(SubEntity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testCreateAfterAccess = true;
    }

    public function reset(){
        $this->testCreateAccess = false;
        $this->testCreateAfterAccess = false;
    }
}
