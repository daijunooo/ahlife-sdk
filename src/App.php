<?php

namespace Ahlife;


use Ahlife\Providers\OCenter;

/**
 * @method OCenter OCenter
 * @method \Ahlife\Contracts\Cache Cache
 */
class App implements \Ahlife\Contracts\App
{
    /**
     * @var App
     */
    protected $app = null;

    protected $config = [];

    protected $providers = [
        'OCenter' => OCenter::class
    ];

    public static $instances = [];


    public function __construct($config = [])
    {
        $this->config = $config;
        $this->app    = $this;
        $this->boot();
    }

    public function boot()
    {
        if (!$boots = $this->config['Boots']) return;

        foreach ($boots as $name => $provider) {
            $this->hasInstance($name) || $this->setInstance($name, $provider);
        }
    }

    public function __call($name, $arguments)
    {
        if (!$this->hasInstance($name)) {
            $this->setInstance($name, $arguments);
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

    /**
     * @param $key string
     */
    public function setInstance($key, $arguments)
    {
        if (array_key_exists($key, $this->providers)) {
            $app = static::$instances[$key] = new $this->providers[$key](...$arguments);
        } else {
            $app = static::$instances[$key] = $arguments::app();
        }
        method_exists($app, 'boot') && $app->boot($this);
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

    /**
     * @param array $config
     * @return array
     */
    public function setConfig($config = [])
    {
        return $this->config = $config;
    }

}
