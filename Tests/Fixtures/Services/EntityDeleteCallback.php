<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntityDeleteCallback
 */
class EntityDeleteCallback
{
    public bool $testDeleteAccess = false;
    public bool $testDeleteAfterAccess = false;

    /**
     * @param Entity                 $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testDelete(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testDeleteAccess = true;
    }

    /**
     * @param Entity                 $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testDeleteAfter(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testDeleteAfterAccess = true;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->testDeleteAccess = false;
        $this->testDeleteAfterAccess = false;
    }
}
