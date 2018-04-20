<?php


namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SubEntityUpdateCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class SubEntityUpdateCallback
{
    public $testUpdateAccess = false;

    public $testUpdateAfterAccess = false;

    public function testUpdate(SubEntity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateAccess = true;
    }

    public function testUpdateAfter(SubEntity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateAfterAccess = true;
    }

    public function reset(){
        $this->testUpdateAccess = false;
        $this->testUpdateAfterAccess = false;
    }
}
