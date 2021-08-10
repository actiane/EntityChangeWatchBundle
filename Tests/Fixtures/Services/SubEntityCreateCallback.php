<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services;

use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntity;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SubEntityCreateCallback
 */
class SubEntityCreateCallback
{
    public bool $testCreateAccess = false;
    public bool $testCreateAfterAccess = false;

    /**
     * @param SubEntity              $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testCreate(SubEntity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testCreateAccess = true;
    }

    /**
     * @param SubEntity              $entity
     * @param                        $changedProperties
     * @param EntityManagerInterface $entityManager
     */
    public function testCreateAfter(SubEntity $entity, $changedProperties, EntityManagerInterface $entityManager)
    {
        $this->testCreateAfterAccess = true;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->testCreateAccess = false;
        $this->testCreateAfterAccess = false;
    }
}
