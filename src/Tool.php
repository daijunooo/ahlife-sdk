<?php

namespace Ahlife;


class Tool implements \Ahlife\Contracts\Tool
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

    public function session($key, $value = null)
    {
        if ($value === null) {
            return session($key);
        } else {
            return $this->session([$key => $value]);
        }
    }


}
