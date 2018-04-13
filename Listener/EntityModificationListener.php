<?php

namespace Actiane\EntityChangeWatchBundle\Listener;


use Actiane\EntityChangeWatchBundle\Generator\LifecycleCallableGenerator;
use Doctrine\ORM\Event\OnFlushEventArgs;

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
        $this->callable = $this->lifecycleCallableGenerator->generateLifeCycleCallable(
            $eventArgs->getEntityManager()->getUnitOfWork()
        );

        foreach ($this->callable as $key => $callableItem) {
            if ($callableItem['flush'] !== false) {
                continue;
            }
            unset($this->callable[$key]);
            call_user_func_array($callableItem['callable'], $callableItem['parameters']);
        }
    }

    /**
     * Called before the database queries, execute all the Generate $this->callableFlush
     */
    public function postFlush()
    {
        foreach ($this->callable as $key => $callableItem) {
            unset($this->callable[$key]);
            call_user_func_array($callableItem['callable'], $callableItem['parameters']);
        }
    }
}