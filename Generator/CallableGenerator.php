<?php


namespace Actiane\EntityChangeWatchBundle\Generator;

/**
 * Class CallableGenerator
 * @package Actiane\EntityChangeWatchBundle\Generator
 */
class CallableGenerator
{
    /**
     * @var array
     */
    private $callbacks = [];

    /**
     * Generate the arrays used for call_user_func_array
     *
     * @param array $arrayCallable
     * @param       $entity
     *
     * @param       $changedProperties
     *
     * @return array
     */
    public function generateCallable(array $arrayCallable = [], $entity, $changedProperties = null)
    {
        $callable = [];
        if (
            array_key_exists('name', $arrayCallable) &&
            array_key_exists('method', $arrayCallable) &&
            array_key_exists('flush', $arrayCallable)
        ) {
            $callable[$this->generateCallableSignature($arrayCallable, $entity)] = [
                'callable' => [
                    $this->callbacks[$arrayCallable['name']],
                    $arrayCallable['method'],
                ],
                'parameters' => ['entity' => $entity, 'changedProperties' => $changedProperties],
                'flush' => $arrayCallable['flush'],
            ];
        }

        return $callable;
    }

    /**
     * Generate the signature of a callback to ensure that each callback for the same entity is only called once
     *
     * @param array $arrayCallable
     * @param       $entity
     *
     * @return string
     */
    private function generateCallableSignature(array $arrayCallable = [], $entity)
    {
        return $arrayCallable['name'].':'.$arrayCallable['method'].':'.spl_object_hash($entity);
    }

    public function addCallback($key, $reference)
    {
        $this->callbacks[$key] = $reference;
    }
}
