<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EntityCreateCallback
 */
class EntityCreateCallback
{
    public bool $testCreateAccess = false;
    public bool $testCreateAfterAccess = false;
    public bool $testResultWithoutEntityManager = false;

    /**
     * @param Entity                 $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testCreate(Entity $entity, $changedProperties)
    {
        $this->testCreateAccess = true;
    }

    /**
     * @param Entity                 $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testCreateAfter(Entity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testCreateAfterAccess = true;
    }

    public function testWithoutEntityManagerParam(Entity $entity): void {
        $this->testResultWithoutEntityManager = true;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->testCreateAccess = false;
        $this->testCreateAfterAccess = false;
        $this->testResultWithoutEntityManager = false;
    }
}
