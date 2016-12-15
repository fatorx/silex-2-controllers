<?php

namespace App\Service;

/**
 * Class AccessService
 * @package App\Service
 */
class AccessService extends BaseService
{

    /**
     * @var string
     */
    protected $token;

    /**
     * @param array $pars
     * @return bool
     */
    public function confirmData(array $pars)
    {
        $this->status = true;

        // @todo code for check login data

        return $this->status;
    }
}