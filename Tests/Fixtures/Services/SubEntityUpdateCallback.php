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

    public function testUpdate(SubEntity $entity, $changedProperties)
    {
        $this->testUpdateAccess = true;
    }

    public function reset(){
        $this->testUpdateAccess = false;
    }
}
