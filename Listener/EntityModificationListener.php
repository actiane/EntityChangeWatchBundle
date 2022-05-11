<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Listener;

use Actiane\EntityChangeWatchBundle\Generator\LifecycleCallableGenerator;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

/**
 * Class EntityModificationListener
 */
class EntityModificationListener
{
    private array $callable = [];
    private LifecycleCallableGenerator $lifecycleCallableGenerator;

    /**
     * @param LifecycleCallableGenerator $lifecycleCallableGenerator
     */
    public function __construct(LifecycleCallableGenerator $lifecycleCallableGenerator)
    {
        $this->lifecycleCallableGenerator = $lifecycleCallableGenerator;
    }

    /**
     * Called before the database queries, execute all the Generate $this->callable
     *
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $entityManager = $eventArgs->getEntityManager();
        $this->callable = $this->lifecycleCallableGenerator->generateLifeCycleCallable(
            $entityManager->getUnitOfWork()
        );

        foreach ($this->callable as $key => $callableItem) {
            if ($callableItem['flush'] !== false) {
                continue;
            }
            unset($this->callable[$key]);
            call_user_func(
                $callableItem['callable'],
                $callableItem['parameters']['entity'],
                $callableItem['parameters']['changedProperties'],
                $entityManager
            );
        }
    }

    /**
     * Called before the database queries, execute all the Generate $this->callableFlush
     *
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs): void
    {
        foreach ($this->callable as $key => $callableItem) {
            unset($this->callable[$key]);
            call_user_func(
                $callableItem['callable'],
                $callableItem['parameters']['entity'],
                $callableItem['parameters']['changedProperties'],
                $eventArgs->getEntityManager()
            );
        }
    }
}
