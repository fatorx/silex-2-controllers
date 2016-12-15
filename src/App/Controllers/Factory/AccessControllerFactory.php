<?php

namespace App\Controllers\Factory;

use Silex\Application;

use App\Service\AccessService;
use App\Controllers\AccessController;

class AccessControllerFactory
{

    /**
     * @var AccessController
     */
    private $controller;

    public function __construct(Application $app)
    {
        $baseService = new AccessService($app['orm.em']);
        $this->controller = new AccessController($baseService);
    }

    public function getController()
    {
        return $this->controller;
    }
}