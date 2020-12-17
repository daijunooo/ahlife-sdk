<?php

namespace Ahlife;

use Firebase\JWT\JWT;

class Auth extends App
{
    protected $secret;
    protected $client;
    protected $exp;

    public function boot()
    {
        $config = self::$config['jwt'];

        $this->exp    = $config['exp'] + time();
        $this->secret = $config['secret'];
        $this->client = $config['client'];
    }

    /**
     * 获取服务网关JWT验证token
     * @return string
     */
    public function getToken()
    {
        $token = [
            'client_id' => $this->client,
            'exp'       => $this->exp,
        ];

        return JWT::encode($token, $this->secret);
    }

}
