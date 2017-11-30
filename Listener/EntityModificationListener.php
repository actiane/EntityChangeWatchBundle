<?php

namespace Actiane\EntityChangeWatchBundle\Listener;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
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
     * PreUpdateListener constructor.
     *
     * @param $entityWatch
     * @param ContainerInterface $serviceContainer
     * @param EntityManager $entityManager
     * @param PropertyAccessor $propertyAccessor
     */
    public function __construct(
        $entityWatch,
        ContainerInterface $serviceContainer,
        EntityManager $entityManager,
        PropertyAccessor $propertyAccessor
    ) {
        $this->entityWatch = $entityWatch;
        $this->serviceContainer = $serviceContainer;
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param array $arrayCallable
     * @param $entity
     *
     * @param $changedProperties
     */
    private function computeCallable(array $arrayCallable = [], $entity, $changedProperties)
    {
        if (array_key_exists('name', $arrayCallable) && array_key_exists('method', $arrayCallable)) {
            $this->callable[$this->computeCallableSignature($arrayCallable, $entity)] = [
                'callable' => [
                    $this->serviceContainer->get($arrayCallable['name']),
                    $arrayCallable['method'],
                ],
                'parameters' => ['entity' => $entity, 'changedProperties'=> $changedProperties],
            ];
        }
    }

    /**
     * @param array $arrayCallable
     * @param $entity
     * @return string
     */
    private function computeCallableSignature(array $arrayCallable = [], $entity)
    {
        return $arrayCallable['name'].':'.$arrayCallable['method'].':'.join(':', $this->computeIdentifierHash($entity));
    }

    /**
     * @param $entity
     * @return array
     */
    private function computeIdentifierHash($entity)
    {
        $metaData = $this->entityManager->getClassMetadata(get_class($entity));

        $identifiers = $metaData->getIdentifierFieldNames();

        $entityIdentifierHashArray = [];
        foreach ($identifiers as $identifier) {

            $value = $this->propertyAccessor->getValue($entity, $identifier);
            if (is_object($value)) {
                $entityIdentifierHashArray = array_merge(
                    $entityIdentifierHashArray,
                    $this->computeIdentifierHash($value)
                );
            } else {
                $entityIdentifierHashArray[] = $value;
            }
        }

        return $entityIdentifierHashArray;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $className = get_class($entity);

        if (array_key_exists($className, $this->entityWatch) &&
            array_key_exists('update', $this->entityWatch[$className])) {

            $entityWatch = $this->entityWatch[$className]['update'];
            if (array_key_exists('all', $entityWatch) && count($args->getEntityChangeSet()) > 0) {
                foreach ($entityWatch['all'] as $action) {
                    $this->computeCallable($action, $entity);
                }
            }

            $collectionChanged = [];
            $scheduledCollectionsUpdates = $args->getObjectManager()->getUnitOfWork()->getScheduledCollectionUpdates();
            foreach ($scheduledCollectionsUpdates as $scheduledCollectionUpdates) {
                $fieldName = $scheduledCollectionUpdates->getMapping()['fieldName'];
                $collectionChanged[$fieldName] = $scheduledCollectionUpdates->getValues();
            }

            $changedProperties = array_merge($collectionChanged, $args->getEntityChangeSet());
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
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $className = get_class($entity);

        if (array_key_exists($className, $this->entityWatch)) {

            foreach ($this->callable as $callableItem) {
                if ($entity === $callableItem['parameters']['entity']) {
                    call_user_func_array($callableItem['callable'], $callableItem['parameters']);
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $className = get_class($entity);

        if (array_key_exists($className, $this->entityWatch) &&
            array_key_exists('create', $this->entityWatch[$className])
        ) {

            foreach ($this->entityWatch[$className]['create'] as $callable) {

                $this->computeCallable($callable, $entity);
            }
            foreach ($this->callable as $callableItem) {
                if ($entity === $callableItem['parameters']['entity']) {
                    call_user_func_array($callableItem['callable'], $callableItem['parameters']);
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postDelete(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $className = get_class($entity);

        if (array_key_exists($className, $this->entityWatch) &&
            array_key_exists('delete', $this->entityWatch[$className])
        ) {

            foreach ($this->entityWatch[$className]['delete'] as $callable) {

                $this->computeCallable($callable, $entity);
            }
            foreach ($this->callable as $callableItem) {
                if ($entity === $callableItem['parameters']['entity']) {
                    call_user_func_array($callableItem['callable'], $callableItem['parameters']);
                }
            }
        }
    }
}