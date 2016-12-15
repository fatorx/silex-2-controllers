<?php

namespace App;


use Silex\Application;

use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;

use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

use Silex\Provider\TwigServiceProvider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Debug\ExceptionHandler;

/**
 * Controller Factories
 */
use App\Controllers\Factory\AccessControllerFactory;
use App\Controllers\Factory\UserControllerFactory;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class LocalApplication extends Application
{

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        ExceptionHandler::register(false);

        $this->before(function(Request $request) {
        });

        $this->init();

        //handling CORS respons with right headers
        $this->after(function(Request $request, Response $response) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        });
    }

    public function init()
    {

        $app['debug'] = !PROD;

        if(!PROD) {
            $whoops = new Run();
            $whoops->pushHandler(new PrettyPageHandler());
            $whoops->register();
        }

        $this->registerProviders();

        $this->error(function (\Exception $e, Request $request, $code) {

            if(PROD) {

                $this->registerLogError($e->getMessage());

                $request->getBaseUrl();
                $request->getMethod();
                $request->getClientIp();
                $data = [
                    'data_layer' => '{}',
                    'logon' => false
                ];

                if ($code == 404) {
                    return $this['twig']->render('site/errors/404.twig', $data);
                }

                return $this['twig']->render('site/errors/generic_error.twig', $data);
            }

            return false;
        });


        $this->mountController();
    }

    public function registerProviders()
    {
        $configDoctrine = include __DIR__.'/../../app/config/doctrine.php';

        $this->register(new DoctrineServiceProvider(), $configDoctrine['db.options']);
        $this->register(new DoctrineOrmServiceProvider(), $configDoctrine);

        $this->register(new ServiceControllerServiceProvider());
        $this->register(new SessionServiceProvider());

        $this->register(new TwigServiceProvider(), [
            'twig.path' => __DIR__.'/../../views',
            'debug' => true
        ]);
    }

    /**
     * @return void
     */
    public function mountController()
    {
        /**
         * Mount Controllers
         */
        $this->mount('/', (new AccessControllerFactory($this))->getController() );
        $this->mount('/user', (new UserControllerFactory($this))->getController() );
    }

    /**
     * @param $message
     */
    public function registerLogError($message)
    {
        $date = new \Datetime();

        $log = new Logger('App');
        $hourControl = $date->format('Y-m-d-H');
        $fileName = 'error_app_'.$hourControl.'.txt';

        $log->pushHandler(new StreamHandler('../app/logs/'.$fileName, Logger::WARNING));
        $log->error($message);
    }

}