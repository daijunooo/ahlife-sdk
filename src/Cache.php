<?php

namespace Ahlife;


class Cache implements \Ahlife\Contracts\Cache
{
    public static function app()
    {
        return new static();
    }

    public function cache($key, $value = '', $expire = 0)
    {
        if ($value) {
            return cache($key, $value, $expire);
        } else {
            return cache($key);
        }
    }

}
