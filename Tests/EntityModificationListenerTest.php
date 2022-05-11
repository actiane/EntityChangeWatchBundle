<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Tests;

use Actiane\EntityChangeWatchBundle\Listener\EntityModificationListener;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\NotWatchedEntity;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntity;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntityOneToOne;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\SubEntityCreateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\SubEntityOneToOneUpdateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\SubEntityUpdateCallback;
use Doctrine\Common\EventManager;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class EntityModificationListenerTest
 */
class EntityModificationListenerTest extends BaseTestCaseORM
{
    protected $listener;
    private const ENTITY = Entity::class;
    private const SUB_ENTITY = SubEntity::class;
    private const SUB_ENTITY_ONE_TO_ONE = SubEntityOneToOne::class;
    private const NOT_WATCHED_ENTITY = NotWatchedEntity::class;

    /**
     * @return string[]
     */
    protected function getUsedEntityFixtures(): array
    {
        return [
            self::ENTITY,
            self::SUB_ENTITY,
            self::SUB_ENTITY_ONE_TO_ONE,
            self::NOT_WATCHED_ENTITY,
        ];
    }

    /**
     *
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->listener = static::getContainer()->get(EntityModificationListener::class);
        $evm = new EventManager();
        $evm->addEventListener(['onFlush', 'postFlush'], $this->listener);
        $this->getMockSqliteEntityManager($evm);
    }

    /**
     * @small
     */
    public function testCrudBeforeFlush(): void
    {
        $entity = new Entity();

        $entity->setTitle('chose');

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $container = static::getContainer();

        $this->assertTrue($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($container->get(EntityCreateCallback::class)->testResultWithoutEntityManager);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        $test->setTitle('chose2');
        $this->em->flush();
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testResultWithoutEntityManager);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $this->em->remove($test);
        $this->em->flush();
        $this->em->clear();

        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testResultWithoutEntityManager);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertTrue($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->reset();
    }

    /**
     * @small
     */
    public function testCreateOnly(): void
    {
        $entity = new Entity();

        $entity->setTitle('chose');

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $container = static::getContainer();
        $this->assertTrue($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($container->get(EntityCreateCallback::class)->testResultWithoutEntityManager);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $this->em->flush();

        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testResultWithoutEntityManager);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->reset();
    }

    /**
     * @small
     */
    public function testUpdateCollectionAdd(): void
    {
        $entity = new Entity();

        $entity->setTitle('chose');
        $subEntity = new SubEntity();
        $subEntity->setField('1');

        $entity->addSubEntity($subEntity);
        $this->em->persist($subEntity);
        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $container = static::getContainer();
        $this->assertTrue($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($container->get(EntityCreateCallback::class)->testResultWithoutEntityManager);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse($container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

        $this->reset();

        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');

        $subEntity2 = new SubEntity();
        $subEntity2->setField('2');
        $test->addSubEntity($subEntity2);
        $this->em->persist($subEntity2);

        $this->em->flush();

        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertTrue($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse($container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

        $this->reset();
    }

    /**
     * @small
     */
    public function testUpdateCollectionDelete(): void
    {
        $entity = new Entity();

        $entity->setTitle('chose');
        $subEntity = new SubEntity();
        $subEntity->setField('1');

        $entity->addSubEntity($subEntity);
        $this->em->persist($subEntity);

        $subEntity2 = new SubEntity();
        $subEntity2->setField('2');

        $entity->addSubEntity($subEntity2);
        $this->em->persist($subEntity2);
        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $container = static::getContainer();
        $this->assertTrue($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse($container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

        $this->reset();

        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        if ($test instanceof Entity) {
            $test->removeSubEntity($test->getSubEntities()[0]);
        }

        $this->em->flush();

        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertTrue($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->reset();
    }

    /**
     * @small
     */
    public function testUpdateSubEntity(): void
    {
        $subEntity = new SubEntity();
        $subEntity->setField('testUpdateSubEntity_1');

        $this->em->persist($subEntity);
        $this->em->flush();

        $container = static::getContainer();
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse($container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

        $this->reset();
        $id = $subEntity->getId();
        $this->em->clear();

        $test = $this->em->getRepository(self::SUB_ENTITY)->find($id);
        $test->setField('treter');
        $this->em->flush();

        $this->assertFalse($container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(SubEntityUpdateCallback::class)->testUpdateAccess);
        $this->assertTrue($container->get(SubEntityUpdateCallback::class)->testUpdateAfterAccess);
    }

    /**
     * Got an issue with OneToOne relationship.
     * When we retrieve subentity from parent - we got an Proxy object
     *
     * @small
     */
    public function testUpdateSubEntityOneToOne(): void
    {
        $entity = (new Entity())->setTitle('title');
        $subEntity = new SubEntityOneToOne();
        $subEntity->setField('blabla');

        $entity->setSubEntitiesOneToOne($subEntity);

        $this->em->persist($subEntity);
        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $id = $entity->getId();

        /** @var Entity $parentEntity */
        $parentEntity = $this->em->getRepository(self::ENTITY)->find($id);
        $entityToUpdate = $parentEntity->getSubEntitiesOneToOne();
        $entityToUpdate->setField('HA');

        $this->em->flush();

        $this->assertTrue(static::getContainer()->get(SubEntityOneToOneUpdateCallback::class)->testUpdateAccess);
    }

    /**
     * @small
     */
    public function testDeleteSubEntity(): void
    {
        $subEntity = new SubEntity();
        $subEntity->setField('testUpdateSubEntity_1');

        $this->em->persist($subEntity);
        $this->em->flush();

        $this->expectException(ServiceNotFoundException::class);

        $this->em->remove($subEntity);
        $this->em->flush();
    }

    /**
     * @small
     */
    public function testNotWatchedEntity(): void
    {
        $entity = new NotWatchedEntity();
        $entity->setTitle('fdsfs');

        $this->em->persist($entity);
        $this->em->flush();
        $id = $entity->getId();
        $this->em->clear();

        $test = $this->em->getRepository(self::NOT_WATCHED_ENTITY)->find($id);
        $test->setTitle('fsdfsdfs');
        $this->em->flush();

        $this->em->remove($test);
        $this->em->flush();

        $container = static::getContainer();
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess
        );
        $this->assertFalse(
            $container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse($container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);
    }

    /**
     *
     */
    private function reset(): void
    {
        static::getContainer()->get(EntityCreateCallback::class)->reset();
        static::getContainer()->get(EntityUpdateCallback::class)->reset();
        static::getContainer()->get(EntityDeleteCallback::class)->reset();
        static::getContainer()->get(EntityUpdateSubEntitiesCallback::class)->reset();
        static::getContainer()->get(SubEntityCreateCallback::class)->reset();
        static::getContainer()->get(SubEntityUpdateCallback::class)->reset();
    }
}
