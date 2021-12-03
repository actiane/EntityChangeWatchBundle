<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntityUpdatSubEntitiesCallback
 */
class EntityUpdateSubEntitiesCallback
{
    public bool $testUpdateSubEntitiesAccess = false;
    public bool $testUpdateSubEntitiesAfterAccess = false;

    /**
     * @param Entity                 $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testUpdateSubEntities(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateSubEntitiesAccess = true;
    }

    /**
     * @param Entity                 $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testUpdateSubEntitiesAfter(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testUpdateSubEntitiesAfterAccess = true;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->testUpdateSubEntitiesAccess = false;
        $this->testUpdateSubEntitiesAfterAccess = false;
    }
}
