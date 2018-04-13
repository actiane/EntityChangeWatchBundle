<?php


namespace Actiane\EntityChangeWatchBundle\Tests;

use Actiane\EntityChangeWatchBundle\Generator\CallableGenerator;
use Actiane\EntityChangeWatchBundle\Generator\LifecycleCallableGenerator;
use Actiane\EntityChangeWatchBundle\Listener\EntityModificationListener;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntity;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\SubEntityCreateCallback;
use Doctrine\Common\EventManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class appTest
 * @package Actiane\EntityChangeWatchBundle\Tests
 */
class EntityModificationListenerTest extends BaseTestCaseORM
{
    protected $listener;

    /**
     * @var ContainerInterface
     */
    private $container;

    const ENTITY = "Actiane\\EntityChangeWatchBundle\\Tests\\Fixtures\\Entity\\Entity";

    protected function getUsedEntityFixtures()
    {
        return [
            self::ENTITY,
            "Actiane\\EntityChangeWatchBundle\\Tests\\Fixtures\\Entity\\SubEntity",
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        self::bootKernel();

        $this->container = self::$kernel->getContainer();

        $propertyAccessor = $this->container->get('property_accessor');

        $classes = self::$kernel->getContainer()->getParameter('entity_watch.classes');
        $callableGenerator = new CallableGenerator($this->container);
        $lifecyleCallableGenerator = new LifecycleCallableGenerator($classes, $callableGenerator, $propertyAccessor);
        $this->listener = new EntityModificationListener($lifecyleCallableGenerator);
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


        $this->assertTrue($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        $test->setTitle('chose2');
        $this->em->flush();
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $this->em->remove($test);
        $this->em->flush();
        $this->em->clear();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertTrue($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
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

        $this->assertTrue($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );

        $this->reset();
        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        $this->em->flush();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
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

        $this->assertTrue($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($this->container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

        $this->reset();

        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        //$test->removeSubEntity($entity->getSubEntities()[0]);


        $subEntity2 = new SubEntity();
        $subEntity2->setField('2');
        $test->addSubEntity($subEntity2);
        $this->em->persist($subEntity2);

        $this->em->flush();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertTrue($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($this->container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);

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

        $this->assertTrue($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($this->container->get(SubEntityCreateCallback::class)->testCreateAfterAccess);


        $this->reset();

        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        if ($test instanceof Entity) {
            $test->removeSubEntity($test->getSubEntities()[0]);
        }

        $this->em->flush();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAfterAccess);
        $this->assertTrue($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAfterAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAfterAccess);
        $this->assertTrue($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse(
            $this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAfterAccess
        );
        $this->reset();
    }

    private function reset(): void
    {
        $this->container->get(EntityCreateCallback::class)->reset();
        $this->container->get(EntityUpdateCallback::class)->reset();
        $this->container->get(EntityDeleteCallback::class)->reset();
        $this->container->get(EntityUpdateSubEntitiesCallback::class)->reset();
        $this->container->get(SubEntityCreateCallback::class)->reset();
    }
}
