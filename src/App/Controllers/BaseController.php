<?php

namespace App\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;
use Twig_Environment;

class BaseController
{

    /**
     * @var \Silex\ControllerCollection
     */
    public $controllers;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Twig_Environment
     */
    protected $view;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Twig_Environment $view
     */
    public function setTemplateControl(Twig_Environment $view)
    {
        $this->view = $view;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param $route
     * @param $method
     */
    public function get($route, $method)
    {
        $this->controllers->get($route, function(Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            return $this->$method();
        });
    }

    /**
     * @param $route
     * @param $method
     */
    public function post($route, $method)
    {
        $this->controllers->post($route, function(Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            return $this->$method();
        });
    }

    /**
     * @param $route
     * @param $method
     */
    public function put($route, $method)
    {
        $this->controllers->put($route, function(Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            return $this->$method();
        });
    }

    /**
     * @param $route
     * @param $method
     */
    public function delete($route, $method)
    {
        $this->controllers->delete($route, function(Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            return $this->$method();
        });
    }

    /**
     * @param $route
     * @param $method
     */
    public function patch($route, $method)
    {
        $this->controllers->patch($route, function(Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            return $this->$method();
        });
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $requestVars = [];
        if($_SERVER['REQUEST_METHOD'] == 'PUT') {
            parse_str(file_get_contents("php://input"), $requestVars);
        } else if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $requestVars = $_POST;
        } else if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $requestVars = $_GET;
        }
        return $requestVars;
    }

    /**
     * @param $name
     * @return string
     */
    public function getParameter($name)
    {
        $pars = $this->getParameters();
        return isset($pars[$name]) ? $pars[$name] : '';
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return null|Request
     * @throws \Exception
     */
    public function getRequest()
    {
        $request = null;
        if($this->request instanceof Request) {
            $request = $this->request;
        } else {
            throw new \Exception("Request object not set!");
        }
        return $request;
    }

    /**
     * @return array
     */
    public function getPartsUri()
    {
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $partsUri = explode('/', $uri);

        return [
            'par1' => $partsUri[1],
            'par2' => isset($partsUri[2]) ? $partsUri[2] : '',
            'par3' => isset($partsUri[3]) ? $partsUri[3] : '',
        ];
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * @param $key
     * @return mixed|null
     * @throws \Exception
     */
    public function getClassInSession($key)
    {

        if ($this->getSession()->has($key)) {
            try {
                $content = $this->getSession()->get($key);
                if(!is_string($content)) {
                    return $content;
                }
                return unserialize($content);
            } catch (\Exception $e) {
                echo  $e->getMessage();
            }
        }

        return null;
    }

    /**
     * @param $key
     * @param $clazz
     */
    public function setClassInSession($key, $clazz)
    {
        $this->getSession()->set($key, serialize($clazz));
    }

    /**
     * @return bool
     */
    protected function checkAccess()
    {
        return true;
    }

    public function redirect($url)
    {
        header('Location:' . $url);
        exit();
    }

    public function render($tpl, $data = [], $prefix = 'site')
    {
        $data['data_layer'] = '{}';
        $data['logon'] = false;

        $template = $prefix . '/' . $tpl;
        return $this->view->render($template, $data);
    }

    public function removeSessionVars()
    {
        $this->session->clear();
    }
}