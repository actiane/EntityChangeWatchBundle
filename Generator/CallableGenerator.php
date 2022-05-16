<?php declare(strict_types = 1);

namespace Actiane\EntityChangeWatchBundle\Generator;

use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Class CallableGenerator
 */
class CallableGenerator
{
    private ServiceLocator $serviceLocator;

    /**
     * @param ServiceLocator $serviceContainer
     */
    public function __construct(ServiceLocator $serviceContainer)
    {
        $this->serviceLocator = $serviceContainer;
    }

    /**
     * Generate the arrays used for call_user_func_array
     *
     * @param object $entity
     *
     * @param array  $arrayCallable
     * @param null   $changedProperties
     *
     * @return array
     */
    public function generateCallable(object $entity, array $arrayCallable = [], $changedProperties = null): array
    {
        $callable = [];

        $callable[$this->generateCallableSignature($entity, $arrayCallable)] = [
            'callable' => [
                $this->serviceLocator->get($arrayCallable['name']),
                $arrayCallable['method'],
            ],
            'parameters' => ['entity' => $entity, 'changedProperties' => $changedProperties],
            'flush' => $arrayCallable['flush'],
        ];

        return $callable;
    }

    /**
     * Generate the signature of a callback to ensure that each callback for the same entity is only called once
     *
     * @param object $entity
     * @param array  $arrayCallable
     *
     * @return string
     */
    private function generateCallableSignature(object $entity, array $arrayCallable = [], ): string
    {
        return $arrayCallable['name'].':'.$arrayCallable['method'].':'.spl_object_hash($entity);
    }
}
