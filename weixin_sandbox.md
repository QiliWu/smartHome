&emsp;&emsp;step1&emsp;安装`XAMPP`并进行配置

<div align="center"><img src="/img/XAMPP配置1.png" width=800px/></div>
<br/>

&emsp;&emsp;&emsp;&emsp;&emsp;打开Apache(httpd.conf)，把所有的`80`都替换成`8000`；打开Apache(httpd-ssl.conf)，把所有的`443`都替换成`4430`

<div align="center"><img src="/img/XAMPP配置2.png" width=750px/></div>
<br/>

&emsp;&emsp;step2&emsp;在`C:/xmapp/htdocs/arduino/`下新建一个`weixin.php`，内容如下

```php
<?php
    //https://blog.csdn.net/websites/article/details/19291915
    define("TOKEN", "Hello");
    $wechatObj = new wechatCallbackapiTest();
    $wechatObj->valid();

    class wechatCallbackapiTest
    {
        public function valid()
        {
            $echoStr = $_GET["echostr"];

            if($this->checkSignature()){
                echo $echoStr;
                exit;
            }
        }

        private function checkSignature()
        {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];

            $token = TOKEN;
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr);

            if( sha1(implode($tmpArr)) == $signature ){
                return true;
            }else{
                return false;
            }
        }
    }
?>  
```

&emsp;&emsp;step3&emsp;重启`XAMPP`，单击第1、2个`Module`的`Start`

&emsp;&emsp;step4&emsp;打开[NATAPP官网](https://natapp.cn/)，注册并登录，购买`VIP_1型`的隧道

<div align="center"><img src="/img/NATAPP配置1.png" width=800px/></div>
<br/>
<div align="center"><img src="/img/NATAPP配置2.png" width=800px/></div>
<br/>

&emsp;&emsp;&emsp;&emsp;&emsp;点击左侧的`二级域名`，注册一个二级域名(如：xyz.nat200.top)，注册好之后点击左侧的`我的隧道`，点击绿色的`配置`进行配置

<div align="center"><img src="/img/NATAPP配置3.png" width=800px/></div>
<br/>
<div align="center"><img src="/img/NATAPP配置4.png" width=800px/></div>
<br/>

&emsp;&emsp;step5&emsp;打开`Desktop/smartHome-master/src/server/natapp_win64_v2.3.8/config.ini`，填写里面的`authtoken`字段

&emsp;&emsp;step6&emsp;双击`natapp.exe`，让它在后台运行

&emsp;&emsp;step7&emsp;打开[微信公众平台接口测试账号申请](http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login)，用个人微信`扫一扫`登录

&emsp;&emsp;step8&emsp;填写`NATAPP`提供的URL，此处的Token应与`weixin.php`中的一致，单击`提交`即可通过认证

<div align="center"><img src="/img/配置URL和Token.png" width=800px/></div>

&emsp;&emsp;step9&emsp;用`Desktop/smartHome-master/src/server/lpx_weixin.php`的内容替换`C:/xmapp/htdocs/arduino/weixin.php`原有的内容

&emsp;&emsp;step10&emsp;返回[README.md](README.md)
