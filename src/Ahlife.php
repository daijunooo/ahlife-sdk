<?php

namespace Ahlife;

/**
 * @method static Timer timer()
 * @method static Auth auth()
 * @property Timer timer
 * @property Auth auth
 */
class Ahlife extends App
{
    public function __construct()
    {
        $this->app = $this;
    }

    public static function __callStatic($name, $arguments)
    {
        if (!isset(self::$instance['ahlife'])) {
            self::$instance['ahlife'] = new static();
        }
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = new self::$services[$name](self::$instance['ahlife']);
        }
        return self::$instance[$name];
    }

    public function __get($name)
    {
        return self::__callStatic($name, []);
    }

}
