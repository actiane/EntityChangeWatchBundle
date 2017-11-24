<?php


namespace Actiane\EntityChangeWatchBundle\Interfaces;


interface InterfaceHelper
{
    /**
     * Compute the signature of the callable, used to avoid a callable to be called multiple times in a row
     *
     * @param array $callable
     * @param array $parameters
     *
     * @return mixed
     */
    public function computeSignature(array $callable, array $parameters);
}