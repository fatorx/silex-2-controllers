<?php

namespace App\Service;

/**
 * Class UserService
 * @package App\Service
 */
class UserService extends BaseService
{

    /**
     * @param array $data
     * @return bool
     */
    public function register(array $data)
    {
        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function access(array $data)
    {
        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function auth(array $data)
    {
        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        return true;
    }

}