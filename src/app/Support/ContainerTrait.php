<?php

namespace Support;

use Interop\Container\ContainerInterface;
use Slim\Container;

trait ContainerTrait
{
    /** @var Container $container */
    protected $container;

    /**
     * ContainerTrait constructor.
     * @param Container $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * @param Container $container
     * @return $this
     * @author Vasil.Rashkov
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return Container
     * @author Vasil.Rashkov
     */
    public function getContainer()
    {
        return $this->container;
    }
}
