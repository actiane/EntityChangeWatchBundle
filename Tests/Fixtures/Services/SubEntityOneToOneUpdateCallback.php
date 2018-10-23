<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/10/18
 * Time: 17:02
 */

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntityOneToOne;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SubEntityOneToOneUpdateCallback
 * @package Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services
 */
class SubEntityOneToOneUpdateCallback
{
    public $testUpdateAccess = false;

    public function testUpdate(SubEntityOneToOne $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateAccess = true;
    }

    public function reset()
    {
        $this->testUpdateAccess = false;
    }
}
