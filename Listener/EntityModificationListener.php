<?php

namespace Actiane\EntityChangeWatchBundle\Listener;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class EntityModificationListener
 * @package Actiane\EntityChangeWatchBundle\Listener
 */
class EntityModificationListener
{
    /**
     * @var array
     */
    private $entityWatch;

    /**
     * @var array
     */
    private $callable = [];

    /**
     * @var array
     */
    private $callableFlush = [];

    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param $entityWatch
     * @param ContainerInterface $serviceContainer
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(
        $entityWatch,
        ContainerInterface $serviceContainer,
        PropertyAccessor $propertyAccessor
    ) {
        $this->entityWatch = $entityWatch;
        $this->serviceContainer = $serviceContainer;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * Compute the arrays used for call_user_func_array
     *
     * @param array $arrayCallable
     * @param $entity
     *
     * @param $changedProperties
     */
    private function computeCallable(array $arrayCallable = [], $entity, $changedProperties = null)
    {
        if (
            array_key_exists('name', $arrayCallable) &&
            array_key_exists('method', $arrayCallable) &&
            array_key_exists('flush', $arrayCallable)
        ) {
            if ($arrayCallable['flush'] === true){
                $this->callableFlush[$this->computeCallableSignature($arrayCallable, $entity)] = [
                    'callable' => [
                        $this->serviceContainer->get($arrayCallable['name']),
                        $arrayCallable['method'],
                    ],
                    'parameters' => ['entity' => $entity, 'changedProperties' => $changedProperties],
                ];
            }else{
                $this->callable[$this->computeCallableSignature($arrayCallable, $entity)] = [
                    'callable' => [
                        $this->serviceContainer->get($arrayCallable['name']),
                        $arrayCallable['method'],
                    ],
                    'parameters' => ['entity' => $entity, 'changedProperties' => $changedProperties],
                ];
            }
        }
    }

    /**
     * Compute the signature of a callback to ensure that each callback for the same entity is only called once
     *
     * @param array $arrayCallable
     * @param $entity
     * @return string
     */
    private function computeCallableSignature(array $arrayCallable = [], $entity)
    {
        return $arrayCallable['name'].':'.$arrayCallable['method'].':'.spl_object_hash($entity);
    }

    /**
     * Called before the database queries, execute all the computed callbacks
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        foreach ($this->callable as $key => $callableItem) {
            unset($this->callable[$key]);
            call_user_func_array($callableItem['callable'], $callableItem['parameters']);
        }
    }

    /**
     * Called before the database queries, execute all the computed callbacks
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        foreach ($this->callableFlush as $key => $callableItem) {
            unset($this->callableFlush[$key]);
            call_user_func_array($callableItem['callable'], $callableItem['parameters']);
        }
    }

    /**
     * Compute callbacks configured as update
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->entityManager = $args->getEntityManager();
        $entity = $args->getEntity();
        $className = get_class($entity);

        if (array_key_exists($className, $this->entityWatch) &&
            array_key_exists('update', $this->entityWatch[$className])) {

            $collectionChanged = [];
            $scheduledCollectionsUpdates = $args->getObjectManager()->getUnitOfWork()->getScheduledCollectionUpdates();
            foreach ($scheduledCollectionsUpdates as $scheduledCollectionUpdates) {
                $fieldName = $scheduledCollectionUpdates->getMapping()['fieldName'];
                $collectionChanged[$fieldName] = $scheduledCollectionUpdates->getValues();
            }

            $changedProperties = array_merge($collectionChanged, $args->getEntityChangeSet());

            $entityWatch = $this->entityWatch[$className]['update'];
            if (array_key_exists('all', $entityWatch) && count($args->getEntityChangeSet()) > 0) {
                foreach ($entityWatch['all'] as $action) {
                    $this->computeCallable($action, $entity, $changedProperties);
                }
            }

            if (array_key_exists('properties', $entityWatch)) {
                foreach ($entityWatch['properties'] as $propertyName => $actions) {
                    if (array_key_exists($propertyName, $changedProperties)) {
                        foreach ($actions as $action) {
                            $this->computeCallable($action, $entity, $changedProperties);
                        }
                    }
                }
            }
        }
    }

    /**
     * Compute callbacks configured as create
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->persist($args);
    }

    /**
     * Compute callbacks configured as create and flush true
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->persist($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function persist(LifecycleEventArgs $args): void
    {
        $this->entityManager = $args->getEntityManager();
        $entity = $args->getEntity();
        $this->computeLifecycleCallables($entity, 'create');
    }

    /**
     * Compute callbacks configured as delete
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->entityManager = $args->getEntityManager();
        $entity = $args->getEntity();
        $this->computeLifecycleCallables($entity, 'delete');
    }

    /**
     * @param $entity
     */
    private function computeLifecycleCallables($entity, $state): void
    {
        $className = get_class($entity);

        if (array_key_exists($className, $this->entityWatch) &&
            array_key_exists($state, $this->entityWatch[$className])
        ) {

            foreach ($this->entityWatch[$className][$state] as $callable) {
                $this->computeCallable($callable, $entity);
            }
        }
    }
}