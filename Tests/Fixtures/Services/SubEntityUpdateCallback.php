<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SubEntityUpdateCallback
 */
class SubEntityUpdateCallback
{
    public bool $testUpdateAccess = false;
    public bool $testUpdateAfterAccess = false;

    /**
     * @param SubEntity              $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testUpdate(SubEntity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateAccess = true;
    }

    /**
     * @param SubEntity              $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testUpdateAfter(SubEntity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateAfterAccess = true;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->testUpdateAccess = false;
        $this->testUpdateAfterAccess = false;
    }
}
