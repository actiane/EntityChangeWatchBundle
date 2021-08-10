<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntityOneToOne;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SubEntityOneToOneUpdateCallback
 */
class SubEntityOneToOneUpdateCallback
{
    public bool $testUpdateAccess = false;

    /**
     * @param SubEntityOneToOne      $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testUpdate(SubEntityOneToOne $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateAccess = true;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->testUpdateAccess = false;
    }
}
