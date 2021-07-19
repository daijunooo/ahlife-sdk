<?php

namespace Ahlife\Contracts;


interface Tool extends App
{
    public function cache($key, $value, $expire);

    public function session($key, $value);
}
