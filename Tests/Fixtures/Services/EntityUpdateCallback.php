<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntityUpdateCallback
 */
class EntityUpdateCallback
{
    public bool $testUpdateAccess = false;
    public bool $testUpdateAfterAccess = false;

    /**
     * @param Entity                 $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testUpdate(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateAccess = true;
    }

    /**
     * @param Entity                 $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testUpdateAfter(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
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
