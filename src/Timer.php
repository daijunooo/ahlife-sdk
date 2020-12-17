<?php

namespace Ahlife;

class Timer extends App
{
    protected $url;

    public function boot()
    {
        $this->url = self::$config['timer']['url'];
    }

    public function timeAsc()
    {
        $format = '%s?%s';

        $params = [
            'token' => $this->app->auth->getToken()
        ];

        return sprintf($format, $this->url, http_build_query($params));
    }
}
