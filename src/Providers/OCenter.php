<?php

namespace Ahlife\Providers;


use Ahlife\App;
use Ahlife\Contracts\Tool;
use Ahlife\Contracts\Sdk;
use Ahlife\Exceptions\Error;

class OCenter extends Sdk
{
    /**
     * @var Tool
     */
    protected $tools;

    /**
     * @var Http
     */
    protected $http;

    protected $server_uri;
    protected $session_key;
    protected $ts_salt;
    protected $wechat;

    /**
     * OCenter constructor.
     * @param $wechat string 授权应用，目前可用应用ahlife、we5、test
     */
    public function __construct($wechat)
    {
        $this->wechat = $wechat;
    }

    public function boot(App $app)
    {
        $this->tools       = $app->tools();
        $this->http        = $app->http();
        $this->server_uri  = $app->getConfig('OCenter.server_uri');
        $this->session_key = $app->getConfig('OCenter.session_key');
        $this->ts_salt     = $app->getConfig('OCenter.ts_salt');
        $this->wechat      = $app->getConfig('OCenter.app');
    }

    /**
     * jssdk
     */
    public function js(array $APIs, $debug = false, $json = true, $url = false)
    {
        $url       = $url ?: self::current();
        $timestamp = time();
        $nonce     = uniqid('rand_');

        list($ticket, $appid) = $this->jsApiTicket();

        $config = [
            'debug'     => $debug,
            'beta'      => false,
            'jsApiList' => $APIs,
            'appId'     => $appid,
            'nonceStr'  => $nonce,
            'timestamp' => $timestamp,
            'url'       => $url,
            'signature' => sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}")
        ];

        return $json ? json_encode($config) : $config;
    }


    /**
     * @return array
     */
    public function jsApiTicket()
    {
        $c_key  = 'JSAPI_TICKET_' . $this->wechat;
        $ticket = $this->tools->cache($c_key);

        if (!$ticket) {
            $res = $this->http->get($this->server_uri, [
                's'   => 'oauth/token/jsapi_ticket',
                'app' => $this->wechat
            ]);

            if ($res['status'] == '200' && $data = json_decode($res['data'], true)) {
                $ticket = [$data['ticket'], $data['appId']];
                $this->tools->cache($c_key, $ticket, $data['expire_in']);
            }
        }

        return $ticket;
    }


    public function accessToken()
    {
        $c_key  = 'ACCESS_TOKEN_' . $this->wechat;
        $ticket = $this->tools->cache($c_key);

        if (!$ticket) {
            $res = $this->http->get($this->server_uri, [
                's'   => 'oauth/token/access_token',
                'app' => $this->wechat
            ]);

            if ($res['status'] == '200' && $data = json_decode($res['data'], true)) {
                $ticket = $data['token'];
                $this->tools->cache($c_key, $ticket, $data['expire_in']);
            }
        }

        return $ticket;
    }


    /**
     * 公众号网页授权
     */
    public function auth($redirect = null, $detail = false)
    {
        $scope  = $detail ? 'snsapi_userinfo' : 'snsapi_base';
        $key    = $this->session_key . $scope;
        $openid = $_REQUEST['openid'];

        if ($user = $this->tools->session($key)) {
            return $user;
        }

        if ($openid && $openid = $this->think_decrypt($openid, $this->ts_salt)) {
            $this->tools->session([$key => ['openid' => $openid]]);
            if ($detail) {
                $res  = $this->http->get($this->server_uri . '?s=/oauth/index/info&openid=' . $openid);
                $data = json_decode($res['data'], true);
                if ($data['status'] && $data['info']['auth'] = true) {
                    return $data['info'];
                } else {
                    throw new Error($data['info'], Error::WECHATAUTH);
                }
            }
        } else {
            $query = [
                'redirect' => $redirect ? $redirect : self::current(),
                'scope'    => $scope,
                'app'      => $this->wechat,
                'salt'     => $this->ts_salt
            ];

            if (request()->ajax()) {
                throw new Error(Error::NOAUTH, '请授权登录');
            }

            throw new Error($this->server_uri . '?s=/oauth&' . http_build_query($query), Error::DIRECT);
        }

    }

    /**
     * @return string
     */
    protected static function current()
    {
        $protocol = (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } else {
            $host = $_SERVER['HTTP_HOST'];
        }

        return $protocol . $host . $_SERVER['REQUEST_URI'];
    }

    private function think_decrypt($data, $key = '')
    {
        $key  = md5($key);
        $data = str_replace(['-', '_'], ['+', '/'], $data);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data   = base64_decode($data);
        $expire = substr($data, 0, 10);
        $data   = substr($data, 10);

        if ($expire > 0 && $expire < time()) {
            return '';
        }
        $x    = 0;
        $len  = strlen($data);
        $l    = strlen($key);
        $char = $str = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return base64_decode($str);
    }

}
