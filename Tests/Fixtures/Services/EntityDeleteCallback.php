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

    public function testDelete(Entity $entity, $changedProperties)
    {
        $this->testDeleteAccess = true;
    }

    public function reset(){
        $this->testDeleteAccess = false;
    }
}
