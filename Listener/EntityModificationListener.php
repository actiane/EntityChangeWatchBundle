<?php

namespace Actiane\EntityChangeWatchBundle\Listener;


use Actiane\EntityChangeWatchBundle\Generator\LifecycleCallableGenerator;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

/**
 * Class EntityModificationListener
 * @package Actiane\EntityChangeWatchBundle\Listener
 */
class EntityModificationListener
{
    /**
     * @var array
     */
    private $callable = [];

    /**
     * @var LifecycleCallableGenerator
     */
    private $lifecycleCallableGenerator;

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
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $this->callable = $this->lifecycleCallableGenerator->generateLifeCycleCallable(
            $entityManager->getUnitOfWork()
        );
    }

    /**
     * Called before the database queries, execute all the Generate $this->callableFlush
     *
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        foreach ($this->callable as $key => $callableItem) {
            unset($this->callable[$key]);
            call_user_func_array(
                $callableItem['callable'],
                $callableItem['parameters'] + ['entityManager' => $eventArgs->getEntityManager()]
            );
        }
    }
}