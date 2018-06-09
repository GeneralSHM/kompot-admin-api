<?php

namespace Controller;

use Slim\Container;
use Slim\Http\Request;

abstract class BaseController
{
    /** @var $container Container */
    protected $container;

    /** @var $request Request */
    protected $request;

    public function __construct(Container $container, Request $request)
    {
        $this->setContainer($container);
        $this->request = $request;
    }

    /**
     * @param Request $request
     * @return $this
     * @author Vasil.Rashkov
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Request
     * @author Vasil.Rashkov
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Container
     * @author Vasil.Rashkov
     */
    public function getContainer()
    {
        return $this->container;
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

    
}
