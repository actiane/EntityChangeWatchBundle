<?php


namespace Actiane\EntityChangeWatchBundle\Tests;

use Actiane\EntityChangeWatchBundle\Listener\EntityModificationListener;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\AppKernel;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\Entity;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\NotWatchedEntity;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Entity\SubEntity;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityCreateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityDeleteCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\EntityUpdateSubEntitiesCallback;
use Actiane\EntityChangeWatchBundle\Tests\Fixtures\Services\SubEntityCreateCallback;
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

    /**
     * @var ContainerInterface
     */
    private $container;

    const ENTITY = Entity::class;

    const SUB_ENTITY = SubEntity::class;

    const NOT_WATCHED_ENTITY = NotWatchedEntity::class;

    protected function getUsedEntityFixtures()
    {
        return [
            self::ENTITY,
            self::SUB_ENTITY,
            self::NOT_WATCHED_ENTITY,
        ];
    }

    protected static function createKernel(array $options = [])
    {
        require_once 'Fixtures/AppKernel.php';
        return new AppKernel(isset($options['environment']) ? $options['environment'] : 'test',
                             isset($options['debug']) ? $options['debug'] : true);
    }


    protected function setUp()
    {
        parent::setUp();

        self::bootKernel();

        $this->container = self::$kernel->getContainer();

        $this->listener = $this->container->get(EntityModificationListener::class);
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
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);

        $this->reset();
        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        $test->setTitle('chose2');
        $this->em->flush();
        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);

        $this->reset();
        $this->em->remove($test);
        $this->em->flush();
        $this->em->clear();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertTrue($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
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
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);

        $this->reset();
        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        $this->em->flush();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
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
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);


        $this->reset();

        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');

        $subEntity2 = new SubEntity();
        $subEntity2->setField('2');
        $test->addSubEntity($subEntity2);
        $this->em->persist($subEntity2);

        $this->em->flush();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertTrue($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);


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
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);


        $this->reset();

        $test = $this->em->getRepository(self::ENTITY)->findOneByTitle('chose');
        if ($test instanceof Entity) {
            $test->removeSubEntity($test->getSubEntities()[0]);
        }

        $this->em->flush();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertTrue($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertTrue($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->reset();
    }

    public function testUpdateSubEntity()
    {
        $subEntity = new SubEntity();
        $subEntity->setField('testUpdateSubEntity_1');

        $this->em->persist($subEntity);
        $this->em->flush();

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);


        $this->reset();
        $id = $subEntity->getId();
        $this->em->clear();

        $test = $this->em->getRepository(self::SUB_ENTITY)->find($id);
        $test->setField('treter');
        $this->em->flush();

        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(SubEntityUpdateCallback::class)->testUpdateAccess);
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

    public function testNotWatchedEntity()
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

        $this->assertFalse($this->container->get(EntityCreateCallback::class)->testCreateAccess);
        $this->assertFalse($this->container->get(EntityUpdateCallback::class)->testUpdateAccess);
        $this->assertFalse($this->container->get(EntityDeleteCallback::class)->testDeleteAccess);
        $this->assertFalse($this->container->get(EntityUpdateSubEntitiesCallback::class)->testUpdateSubEntitiesAccess);
        $this->assertFalse($this->container->get(SubEntityCreateCallback::class)->testCreateAccess);
    }

    private function reset(): void
    {
        $this->container->get(EntityCreateCallback::class)->reset();
        $this->container->get(EntityUpdateCallback::class)->reset();
        $this->container->get(EntityDeleteCallback::class)->reset();
        $this->container->get(EntityUpdateSubEntitiesCallback::class)->reset();
        $this->container->get(SubEntityCreateCallback::class)->reset();
        $this->container->get(SubEntityUpdateCallback::class)->reset();
    }
}
