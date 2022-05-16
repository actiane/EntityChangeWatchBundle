<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Generator;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class LifecycleCallableGenerator
 */
class LifecycleCallableGenerator
{
    private array $entityWatch;
    private PropertyAccessor $propertyAccessor;
    private CallableGenerator $callableGenerator;

    public function __construct(array $entityWatch, CallableGenerator $callableGenerator, PropertyAccessor $propertyAccessor)
    {
        $this->entityWatch = $entityWatch;
        $this->propertyAccessor = $propertyAccessor;
        $this->callableGenerator = $callableGenerator;
    }

    /**
     * @param UnitOfWork $unitOfWork
     *
     * @return array
     */
    public function generateLifeCycleCallable(UnitOfWork $unitOfWork): array
    {
        $callable = [];
        $lifeCycleEntities = [];
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entityInsertion) {
            $this->generateCreateDeleteCallables($callable, $lifeCycleEntities, $entityInsertion, 'create');
        }

        foreach ($unitOfWork->getScheduledEntityDeletions() as $entityDeletion) {
            $this->generateCreateDeleteCallables($callable, $lifeCycleEntities, $entityDeletion, 'delete');
        }

        foreach ($unitOfWork->getScheduledCollectionUpdates() as $collectionUpdate) {
            if (array_key_exists(spl_object_hash($collectionUpdate->getOwner()), $lifeCycleEntities)) {
                continue;
            }
            $this->generateUpdateCallables(
                $callable,
                [$collectionUpdate->getMapping()['fieldName'] => $collectionUpdate->getValues()],
                $collectionUpdate->getOwner()
            );
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entityUpdate) {
            $this->generateUpdateCallables(
                $callable,
                $unitOfWork->getEntityChangeSet($entityUpdate),
                $entityUpdate
            );
        }

        return $callable;
    }

    /**
     * @param $callable
     * @param $entity
     * @param $state
     */
    private function generateCreateDeleteCallables(&$callable, &$lifeCycleEntities, $entity, $state): void
    {
        $lifeCycleEntities[spl_object_hash($entity)] = $entity;
        // we might have a doctrine proxyfied object
        $className = ClassUtils::getClass($entity);

        if (!array_key_exists($className, $this->entityWatch)) {
            return;
        }

        foreach ($this->entityWatch[$className][$state] as $action) {
            $callable += $this->callableGenerator->generateCallable($entity, $action);
        }
    }

    /**
     * @param $callable
     * @param $changedProperties
     * @param $entity
     */
    private function generateUpdateCallables(&$callable, $changedProperties, $entity): void
    {
        // we might have a doctrine proxyfied object
        $className = ClassUtils::getClass($entity);

        if (!array_key_exists($className, $this->entityWatch)
            || !array_key_exists('update', $this->entityWatch[$className])
        ) {
            return;
        }

        $entityWatch = $this->entityWatch[$className]['update'];
        foreach ($entityWatch['all'] as $action) {
            $callable += $this->callableGenerator->generateCallable($entity, $action, $changedProperties);
        }

        foreach ($entityWatch['properties'] as $propertyName => $actions) {
            if (array_key_exists($propertyName, $changedProperties)) {
                foreach ($actions as $action) {
                    $callable += $this->callableGenerator->generateCallable(
                        $entity,
                        $action,
                        $changedProperties
                    );
                }
            }
        }
    }
}
