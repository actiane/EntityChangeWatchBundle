<?php

namespace Actiane\EntityChangeWatchBundle\Listener;


use Actiane\EntityChangeWatchBundle\Interfaces\InterfaceHelper;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * PreUpdateListener constructor.
     *
     * @param $entityWatch
     * @param ContainerInterface $serviceContainer
     */
    public function __construct($entityWatch, ContainerInterface $serviceContainer)
    {
        $this->entityWatch = $entityWatch;
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @param array $arrayCallable
     * @param $entity
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    private function computeCallable(array $arrayCallable = [], $entity)
    {
        if (array_key_exists('name', $arrayCallable) && array_key_exists('method', $arrayCallable)) {
            $this->callable[$this->computeCallableSignature($arrayCallable, $entity)] = [
                'callable' => [
                    $this->serviceContainer->get($arrayCallable['name']),
                    $arrayCallable['method'],
                ],
                'parameters' => ['entity' => $entity],
            ];
        }
    }

    private function computeCallableSignature(array $arrayCallable = [], $entity)
    {
        if (!($this->serviceContainer->get($arrayCallable['name']) instanceof InterfaceHelper)) {
            throw new \Exception(
                'Service '.
                $arrayCallable['name'].
                ' must implements Actiane\EntityChangeWatchBundle\Interfaces\InterfaceHelper'
            );
        }

        return call_user_func(
            [
                $this->serviceContainer->get($arrayCallable['name']),
                'computeSignature',
            ],
            [
                $this->serviceContainer->get($arrayCallable['name']),
                $arrayCallable['method'],
            ],
            ['entity' => $entity]
        );
    }

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
                            $this->computeCallable($action, $entity);
                        }
                    }
                }
            }
        }
    }

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

    public function postRemove(LifecycleEventArgs $args)
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