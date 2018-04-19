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

    public function testCreate(SubEntity $entity, $changedProperties)
    {
        $this->testCreateAccess = true;
    }

    public function reset(){
        $this->testCreateAccess = false;
    }
}
