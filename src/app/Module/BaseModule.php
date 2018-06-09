<?php

namespace Module;

use Slim\Container;

abstract class BaseModule
{
    /** @var $container Container */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Container
     * @author Vasil.Rashkov
     */
    protected function getContainer()
    {
        return $this->container;
    }
}
