<?php

namespace Ahlife\Contracts;


interface Cache extends App
{
    public function cache($key, $value, $expire);
}
