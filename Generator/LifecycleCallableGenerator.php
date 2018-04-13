<?php


namespace Actiane\EntityChangeWatchBundle\Generator;

use Doctrine\ORM\UnitOfWork;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class LifecycleCallableGenerator
 * @package Actiane\EntityChangeWatchBundle\Generator
 */
class LifecycleCallableGenerator
{
    /**
     * @var array
     */
    private $entityWatch;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @var CallableGenerator
     */
    private $callableGenerator;

    public function __construct(
        $entityWatch,
        CallableGenerator $callableGenerator,
        PropertyAccessor $propertyAccessor
    ) {
        $this->entityWatch = $entityWatch;
        $this->propertyAccessor = $propertyAccessor;
        $this->callableGenerator = $callableGenerator;
    }

    /**
     * @param UnitOfWork $uow
     *
     * @return array
     */
    public function generateLifeCycleCallable(UnitOfWork $uow): array
    {
        $callable = [];
        $lifeCycleEntities = [];
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $lifeCycleEntities[spl_object_hash($entity)] = $entity;
            $this->generateCreateDeleteCallables($callable, $entity, 'create');
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $lifeCycleEntities[spl_object_hash($entity)] = $entity;
            $this->generateCreateDeleteCallables($callable, $entity, 'delete');
        }

        foreach ($uow->getScheduledCollectionUpdates() as $entity) {
            if (array_key_exists(spl_object_hash($entity->getOwner()), $lifeCycleEntities)) {
                continue;
            }
            $this->generateUpdateCallables(
                $callable,
                [$entity->getMapping()['fieldName'] => $entity->getValues()],
                $entity->getOwner()
            );
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->generateUpdateCallables(
                $callable,
                $uow->getEntityChangeSet($entity),
                $entity
            );
        }

        return $callable;
    }


    /**
     * @param $callable
     * @param $entity
     * @param $state
     */
    private function generateCreateDeleteCallables(&$callable, $entity, $state): void
    {
        $className = get_class($entity);

        if (array_key_exists($className, $this->entityWatch) &&
            array_key_exists($state, $this->entityWatch[$className])
        ) {
            foreach ($this->entityWatch[$className][$state] as $action) {
                $callable += $this->callableGenerator->generateCallable($action, $entity);
            }
        }
    }

    /**
     * @param $callable
     * @param $changedProperties
     * @param $entity
     */
    private function generateUpdateCallables(&$callable, $changedProperties, $entity): void
    {
        $className = get_class($entity);

        $entityWatch = $this->entityWatch[$className]['update'];
        if (array_key_exists('all', $entityWatch) && count($changedProperties) > 0) {
            foreach ($entityWatch['all'] as $action) {
                $callable += $this->callableGenerator->generateCallable($action, $entity, $changedProperties);
            }
        }

        if (array_key_exists('properties', $entityWatch)) {
            foreach ($entityWatch['properties'] as $propertyName => $actions) {
                if (array_key_exists($propertyName, $changedProperties)) {
                    foreach ($actions as $action) {
                        $callable += $this->callableGenerator->generateCallable(
                            $action,
                            $entity,
                            $changedProperties
                        );
                    }
                }
            }
        }
    }
}
