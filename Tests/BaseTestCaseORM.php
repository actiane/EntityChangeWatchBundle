<?php declare(strict_types=1);


namespace Actiane\EntityChangeWatchBundle\Tests;

use Actiane\EntityChangeWatchBundle\Listener\EntityModificationListener;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Connection;

/**
 * Base test case contains common mock objects
 * and functionality among all extensions using
 * ORM object manager
 *
 * @author  Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @link    http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
abstract class BaseTestCaseORM extends KernelTestCase
{
    protected EntityManager $em;

    /**
     * EntityManager mock object together with
     * annotation mapping driver and pdo_sqlite
     * database in memory
     *
     * @param EventManager|null  $evm
     * @param Configuration|null $config
     *
     * @return EntityManager
     */
    protected function getMockSqliteEntityManager(EventManager $evm = null, Configuration $config = null): EntityManager
    {
        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => false,
        ];
        $config = null === $config ? $this->getMockAnnotatedConfig() : $config;
        $em = EntityManager::create($conn, $config, $evm ?: $this->getEventManager());
        $schema = array_map(
            static function ($class) use ($em) {
                return $em->getClassMetadata($class);
            },
            (array)$this->getUsedEntityFixtures()
        );
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema([]);
        $schemaTool->createSchema($schema);

        return $this->em = $em;
    }

    /**
     * EntityManager mock object together with
     * annotation mapping driver and custom
     * connection
     *
     * @param array             $conn
     * @param EventManager|null $evm
     *
     * @return EntityManager
     */
    protected function getMockCustomEntityManager(array $conn, EventManager $evm = null): EntityManager
    {
        $config = $this->getMockAnnotatedConfig();
        $em = EntityManager::create($conn, $config, $evm ?: $this->getEventManager());
        $schema = array_map(
            static function ($class) use ($em) {
                return $em->getClassMetadata($class);
            },
            (array)$this->getUsedEntityFixtures()
        );
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropSchema([]);
        $schemaTool->createSchema($schema);

        return $this->em = $em;
    }

    /**
     * EntityManager mock object with
     * annotation mapping driver
     *
     * @param EventManager|null $evm
     *
     * @return EntityManager
     */
    protected function getMockMappedEntityManager(EventManager $evm = null): EntityManager
    {
        $driver = $this->getMockBuilder(Driver::class)->getMock();
        $driver->expects($this->once())
               ->method('getDatabasePlatform')
               ->will($this->returnValue($this->getMockBuilder(MySqlPlatform::class)->getMock()))
        ;
        $conn = $this->getMockBuilder(Connection::class)
                     ->setConstructorArgs([], $driver)
                     ->getMock()
        ;
        $conn->expects($this->once())
             ->method('getEventManager')
             ->will($this->returnValue($evm ?: $this->getEventManager()))
        ;
        $config = $this->getMockAnnotatedConfig();
        $this->em = EntityManager::create($conn, $config);

        return $this->em;
    }

    /**
     * Creates default mapping driver
     *
     * @return AnnotationDriver
     */
    protected function getMetadataDriverImplementation(): AnnotationDriver
    {
        return new AnnotationDriver($_ENV['annotation_reader']);
    }

    /**
     * Get a list of used fixture classes
     *
     * @return array
     */
    abstract protected function getUsedEntityFixtures(): array;

    /**
     * Build event manager
     *
     * @return EventManager
     */
    private function getEventManager(): EventManager
    {
        $evm = new EventManager();
        $evm->addEventListener(['preUpdate', 'prePersist', 'postPersist', 'preRemove', 'onFlush', 'postFlush'], new EntityModificationListener());

        return $evm;
    }

    /**
     * Get annotation mapping configuration
     *
     * @return Configuration
     */
    protected function getMockAnnotatedConfig(): Configuration
    {
        $config = new Configuration();
        $config->setProxyDir(__DIR__.'/../../temp');
        $config->setProxyNamespace('Proxy');
        $config->setMetadataDriverImpl($this->getMetadataDriverImplementation());

        return $config;
    }
}
