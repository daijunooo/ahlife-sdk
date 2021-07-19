<?php

namespace Ahlife\Contracts;


use Ahlife\App;

abstract class Sdk
{
    abstract function boot(App $app);

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

}
