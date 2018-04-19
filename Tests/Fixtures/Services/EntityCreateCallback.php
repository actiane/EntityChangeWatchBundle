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

    public function testCreate(Entity $entity, $changedProperties)
    {
        $this->testCreateAccess = true;
    }

    public function reset(){
        $this->testCreateAccess = false;
    }
}
