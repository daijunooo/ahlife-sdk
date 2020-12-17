<?php

namespace Ahlife;

class App
{
    /**
     * @var Ahlife
     */
    protected $app;

    /**
     * 服务实例容器
     */
    public static $instance;

    /**
     * 可用服务列表
     */
    public static $services = [
        'timer' => Timer::class,
        'auth'  => Auth::class,
    ];

    public static $config = [
        'jwt'   => [
            'secret' => 'xxxxxxx',
            'client' => 'xxxxxxx',
            'exp'    => 3600
        ],
        'timer' => [
            'url' => 'http://localhost:8000/baidu'
        ]
    ];

    public function __construct(App $app)
    {
        $this->app = $app;
        method_exists($this, 'boot') && $this->boot();
    }

    public static function setConfig(array $config)
    {
        self::$config = array_merge(self::$config, $config);
    }
}
