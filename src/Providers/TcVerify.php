<?php

namespace Ahlife\Providers;

use Ahlife\App;
use Ahlife\Contracts\Sdk;
use Ahlife\Exceptions\Error;

/**
 * 腾讯人机验证
 */
class TcVerify extends Sdk
{
    protected $appid;
    protected $secret;
    protected $apiurl;
    protected $http;


    public function boot(App $app)
    {
        $this->appid  = $app->getConfig('tcverify.appid');
        $this->secret = $app->getConfig('tcverify.secret');
        $this->apiurl = $app->getConfig('tcverify.apiurl');
        $this->http   = $app->http();
    }

    /**
     * 校验方法
     * @return bool|mixed
     */
    public function verify()
    {
        $response = $this->http->get($this->apiurl, [
            "aid"          => $this->appid,
            "AppSecretKey" => $this->secret,
            "Ticket"       => $_REQUEST['ticket'],
            "Randstr"      => $_REQUEST['randstr'],
            "UserIP"       => $_SERVER['SERVER_ADDR']
        ]);

        $result = json_decode($response['data']);

        if ($result && $result->response == 1) {
            return true;
        } else {
            throw new Error('非法操作', Error::VERITY);
        }
    }

}
