<?php


namespace Actiane\EntityChangeWatchBundle\Tests;

use Actiane\EntityChangeWatchBundle\Generator\CallableGenerator;
use Actiane\EntityChangeWatchBundle\Generator\LifecycleCallableGenerator;
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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class appTest
 * @package Actiane\EntityChangeWatchBundle\Tests
 */
class EntityModificationListenerTest extends BaseTestCaseORM
{
    protected $listener;

    const ENTITY = Entity::class;

    const SUB_ENTITY = SubEntity::class;

    const SUB_ENTITY_ONE_TO_ONE = SubEntityOneToOne::class;

    const NOT_WATCHED_ENTITY = NotWatchedEntity::class;

    protected function getUsedEntityFixtures()
    {
        return [
            self::ENTITY,
            self::SUB_ENTITY,
            self::SUB_ENTITY_ONE_TO_ONE,
            self::NOT_WATCHED_ENTITY
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        self::bootKernel();

        $this->listener = self::$container->get(EntityModificationListener::class);
        $evm = new EventManager();
        $evm->addEventListener(['onFlush', 'postFlush'], $this->listener);
        $this->getMockSqliteEntityManager($evm);
    }

    public function testCrudBeforeFlush()
    {
        $entity = new Entity();

        $entity->setTitle('chose');

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();


        $this->assertTrue(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        $test->setTitle('chose2');
        $this->em->flush();
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $this->em->remove($test);
        $this->em->flush();
        $this->em->clear();

        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertTrue(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->reset();
    }

    public function testCreateOnly()
    {
        $entity = new Entity();

        $entity->setTitle('chose');

        $this->em->persist($entity);
        $this->em->flush();
        $this->em->clear();

        $this->assertTrue(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        $this->em->flush();

        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->reset();
    }

    public function testUpdateCollectionAdd()
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

        $this->assertTrue(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse(self::$container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue(self::$container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

        $this->reset();

        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        //$test->removeSubEntity($entity->getSubEntities()[0]);


        $subEntity2 = new SubEntity();
        $subEntity2->setField('2');
        $test->addSubEntity($subEntity2);
        $this->em->persist($subEntity2);

        $this->em->flush();

        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertTrue(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse(self::$container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue(self::$container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

        $this->reset();
    }

    public function testUpdateCollectionDelete()
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

        $this->assertTrue(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse(self::$container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue(self::$container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);


        $this->reset();

        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        if ($test instanceof Entity) {
            $test->removeSubEntity($test->getSubEntities()[0]);
        }

        $this->em->flush();

        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertTrue(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->reset();
    }

    public function testUpdateSubEntity()
    {
        $subEntity = new SubEntity();
        $subEntity->setField('testUpdateSubEntity_1');

        $this->em->persist($subEntity);
        $this->em->flush();

        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse(self::$container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue(self::$container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

        $this->reset();
        $id = $subEntity->getId();
        $this->em->clear();

        $test = $this->em->getRepository(self::SUB_ENTITY)->find($id);
        $test->setField('treter');
        $this->em->flush();

        $this->assertFalse(self::$container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(SubEntityUpdateCallback::class)->testUpdateAccess);
        $this->assertTrue(self::$container->get(SubEntityUpdateCallback::class)->testUpdateAfterAccess);
    }

    /**
     * Got an issue with OneToOne relationship.
     * When we retrieve subentity from parent - we got an Proxy object
     */
    public function testUpdateSubEntityOneToOne()
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

        $this->assertTrue(self::$container->get(SubEntityOneToOneUpdateCallback::class)->testUpdateAccess);
    }

    public function testDeleteSubEntity()
    {
        $subEntity = new SubEntity();
        $subEntity->setField('testUpdateSubEntity_1');

        $this->em->persist($subEntity);
        $this->em->flush();

        $this->expectException(ServiceNotFoundException::class);

        $this->em->remove($subEntity);
        $this->em->flush();
    }

    public function testNotWatchedEntity(){
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

        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse(self::$container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse(self::$container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse(self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            self::$container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse(self::$container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse(self::$container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);


    }

    private function reset(): void
    {
        self::$container->get(EntityCreateCallback::class)->reset();
        self::$container->get(EntityUpdateCallback::class)->reset();
        self::$container->get(EntityDeleteCallback::class)->reset();
        self::$container->get(EntityUpdateSubEntitiesCallback::class)->reset();
        self::$container->get(SubEntityCreateCallback::class)->reset();
        self::$container->get(SubEntityUpdateCallback::class)->reset();
    }
}
