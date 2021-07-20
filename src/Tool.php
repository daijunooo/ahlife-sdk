<?php

namespace Ahlife;


class Tool implements \Ahlife\Contracts\Tool
{
    public static function app()
    {
        return new static();
    }

    public function cache($key, $value = null, $expire = null)
    {
        if ($value === null) {
            return cache($key);
        } else {
            return cache([$key => $value], $expire);
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
