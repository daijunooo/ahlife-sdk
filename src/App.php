<?php

namespace Ahlife;


use Ahlife\Providers\Http;
use Ahlife\Providers\OCenter;
use Ahlife\Providers\TcVerify;

/**
 * @method OCenter ocenter
 * @method Http http
 * @method TcVerify tcverify
 * @method \Ahlife\Contracts\Tool tools
 */
class App implements \Ahlife\Contracts\App
{
    /**
     * @var App
     */
    protected $app = null;

    protected $config = [];

    protected $providers = [
        'ocenter'  => OCenter::class,
        'http'     => Http::class,
        'tcverify' => TcVerify::class,
    ];

    public static $instances = [];


    public function __construct($config = [])
    {
        $this->config = $config;
        $this->app    = $this;
        $this->boot();
    }

    /**
     * 加载配置文件中定义的boots服务
     */
    public function boot()
    {
        if (!$boots = $this->config['boots']) return;

        $this->providers = array_merge($this->providers, $this->config['boots']);
    }


    public function __call($name, $arguments)
    {
        if (!$this->hasInstance($name)) {

            $app = $this->setInstance($name, $arguments);

            method_exists($app, 'boot') && $app->boot($this);
        }

        return $this->getInstance($name);
    }


    /**
     * @return App
     */
    public static function app($config = [])
    {
        return new static($config);
    }

    /**
     * @param $key string
     * @return bool
     */
    public function hasInstance($key)
    {
        return isset(static::$instances[$key]);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getInstance($key)
    {
        return static::$instances[$key];
    }


    public function setInstance($key, $arguments = null)
    {
        if (array_key_exists($key, $this->providers)) {
            return static::$instances[$key] = new $this->providers[$key](...$arguments);
        } else {
            die('sdk中没有找到' . $key . '方法');
        }
    }

    /**
     * @param string $key
     * @return array
     */
    public function getConfig($key = '')
    {
        $config = $this->config;

        if (strpos($key, '.') === false) {
            return isset($config[$key]) ? $config[$key] : $config;
        } else {
            $keys = explode('.', $key);

            while ($key = array_shift($keys)) {
                $config = $config[$key];
            }

            return $config;
        }
    }


    public function setConfig($config = [])
    {
        return $this->config = $config;
    }

}
