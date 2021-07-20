# ahlife-sdk
ahlife develop sdk

# 配置

- 复制composer包下的config.php文件到你的项目框架内，填好配置信息。
- 继承Tool.php,改写里面的方法，用你用的框架实现缓存和session（laravel框架可跳过此步骤，默认的就是laravel的）
- 将你的Tool.php类配置到config.php的boots配置项内，覆盖原有的tools配置。（laravel框架跳过）

# 示例

```
$config = require('./config.php');
$app = Ahlife\App::app($config);

// 授权中心
$ocenter = $app->ocenter('we5');
$config = $ocenter->js(['onMenuShareTimeline', 'onMenuShareAppMessage'], false, true);

// 腾讯滑动验证
$tcverify = $app->tcverify();
$tcverify->verify();
```
