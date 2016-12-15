<?php

namespace App\Controllers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends BaseController implements ControllerProviderInterface
{

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $this->controllers = $app['controllers_factory'];

        $this->get('/', 'index');
        $this->post('/', 'registerUser');

        return $this->controllers;
    }

    public function index()
    {
        return "index api";
    }

    /**
     * @return JsonResponse
     */
    public function registerUser()
    {
        $pars = $this->getRequest();

        $status = $this->service->register($pars);
        $data = [
            'status' => $status,
            'content' => $this->service->getData()
        ];
        return new JsonResponse($data);
    }
}