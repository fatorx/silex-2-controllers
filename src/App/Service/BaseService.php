<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;

use GuzzleHttp\Client;

class BaseService
{

    const API_HOST = 'http://localhost';

    /**
     * Data contains return operations
     * @var array
     */
    protected $data = [];

    /**
     * Flag for tracking status of operation
     * @var bool
     */
    protected $status = true;

    /**
     * Message from result operation
     * @var string
     */
    protected $message = '';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * BaseService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->init();
    }

    public function init()
    {
        // @todo implement actions for simulate constructor class
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param int $id
     * @return object
     */
    public function getItem($id)
    {}

    /**
     * @return array
     */
    public function getList()
    {}

    /**
     * @param array $data
     * @return bool
     */
    public function newRegister(array $data)
    {}

    /**
     * @param array $data
     * @return bool
     */
    public function updateRegister(array $data)
    {}

    /**
     * @param int $id
     * @return bool
     */
    public function deleteRegister($id)
    {}

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $uri
     * @param $pars
     * @param string $sendMode = form_params|json
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function getReturnData($uri, $pars, $sendMode = 'form_params')
    {
        $urlRequest = $this->getUrlWs() . $uri;
        if(MOCK_DATA) {
            return $this->getMockData($uri, $pars);
        }

        $client = new Client(['proxy' => '']);

        try {

            $response = $client->request('POST', $urlRequest,
                [$sendMode => $pars]
            );

            if($response->getStatusCode()) {
                $body = $response->getBody();
                $remainingBytes = $body->getContents();

                if(!strstr($remainingBytes,'\\')) {
                    $data = json_decode($remainingBytes, true);
                    $this->registerInfoCallApi($uri, $pars, $data);
                    return $data;
                }

                $str = json_decode($remainingBytes, true);
                if(is_array($str)) {
                    $this->registerInfoCallApi($uri, $pars, $str);
                    return $str;
                }

                $data = json_decode($str, true, 1024);
                $this->registerInfoCallApi($uri, $pars, $data);
                return $data;
            }
        } catch(\Exception $e) {
            $this->registerErrorApi($uri, $pars, $e->getMessage());
            $dataError = [
                'status' => false,
                'message' => 'Erro no sistema.'
            ];

            return $dataError;
        }

        return false;
    }

    public function getUrlWs()
    {
        return self::API_HOST;
    }

    public function registerInfoCallApi($uri, $pars, $dataReturn)
    {
        $date = new \Datetime();
        $hourControl = $date->format('Y-m-d-H');
        $fileName = 'log_'.$hourControl.'.txt';

        $logDesc  = 'URI: '.$uri . ' - ';
        $logDesc .= 'DATETIME:  '.$date->format('Y-m-d H:i:s'). "\n";
        $logDesc .= 'PARS: ' . json_encode($pars)."\n";
        $logDesc .= 'RETURN: ' . json_encode($dataReturn)."\n\n";

        $f = fopen('../.../../app/logs/'.$fileName, 'a+');
        fwrite($f, $logDesc);
        fclose($f);
    }

    public function registerErrorApi($uri, $pars, $error)
    {
        if($uri == '/view/page') {
            return;
        }

        $date = new \Datetime();
        $hourControl = $date->format('Y-m-d-H');
        $fileName = 'error_'.$hourControl.'.txt';

        $logDesc  = 'URI: '.$uri . ' - ';
        $logDesc .= 'DATETIME:  '.$date->format('Y-m-d H:i:s'). "\n";
        $logDesc .= 'PARS: ' . json_encode($pars)."\n";
        $logDesc .= 'ERROR: ' . $error."\n\n";

        $f = fopen('../.../../app/logs/'.$fileName, 'a+');
        fwrite($f, $logDesc);
        fclose($f);
    }

    /**
     * Method for simulate access API's
     *
     * @param $uri
     * @param array $pars
     * @return mixed
     * @throws \Exception
     */
    public function getMockData($uri, $pars = [])
    {
        $mockData = [
            '/login/check-data' => [
                'status' => true,
                'message' => ''
            ],
        ];

        $this->data = [
          'name' => '',
          'phone' => '',
          'email' => '',
        ];

        if(array_key_exists($uri, $mockData)) {
            return $mockData[$uri];
        } else {
            throw new \Exception("Mock Uri Data Not Implement : " . $uri);
        }
    }
}
