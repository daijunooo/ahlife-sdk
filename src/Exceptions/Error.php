<?php

namespace Ahlife\Exceptions;


class Error extends \Exception
{
    // 人机验证
    const VERITY = 3003;
    // 微信授权
    const WECHATAUTH = 3001;
    // 需要微信授权登录
    const NOAUTH = 40001;
    // 重定向
    const DIRECT = 302;

}
