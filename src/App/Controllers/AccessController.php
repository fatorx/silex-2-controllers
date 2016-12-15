<?php

namespace App\Controllers;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Service\AccessService;

class AccessController extends BaseController implements ControllerProviderInterface
{

    /**
     * @var AccessService
     */
    public $service;

    /**
     * AccessController constructor.
     * @param AccessService $baseService
     */
    public function __construct(AccessService $baseService)
    {
        $this->service = $baseService;
    }

    /**
     * @param Application $app
     * @return mixed|\Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->controllers = $app['controllers_factory'];

        $this->get('/', 'index');
        $this->post('/login', 'login');
        $this->get('/logoff', 'logoff');

        return $this->controllers;
    }

    /**
     * @return string
     */
    public function index()
    {
        $data = [];
        return $this->render('access/index.twig', $data);
    }

    /**
     * @todo add code for error operations.
     *
     * @return JsonResponse
     */
    public function login()
    {
        $pars = $this->getParameters();
        $isAccess = $this->service->confirmData($pars);

        $data = [
            'status' => $isAccess,
            'message' => $this->service->getMessage()
        ];

        return new JsonResponse($data);
    }

    /**
     * @return void
     */
    public function logoff()
    {
        $this->removeSessionVars();
        $this->redirect('/');
    }
}