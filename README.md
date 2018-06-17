***
## 1.介绍
- 功能
  - 通过微信获取温湿度、光照强度、烟雾浓度
  - 通过微信远程控制插座、空调、门栓

<div align="center"><img src="/img/效果.png" width=300px/></div>

- 硬件
  - [Arduino Uno R3&emsp;(2个)](https://item.taobao.com/item.htm?id=520691932778)
  - [Wi-Fi模块&emsp;(2个)](https://item.taobao.com/item.htm?id=521608192488)
  - 
  - [插座](https://detail.tmall.com/item.htm?id=45339692426)
  - [剥线钳](https://detail.tmall.com/item.htm?id=44633548750)
  - [继电器](https://detail.tmall.com/item.htm?id=41254925223)
  - 
  - [温湿度传感器](https://detail.tmall.com/item.htm?id=41248630584)
  - [光照强度传感器](https://detail.tmall.com/item.htm?id=41209907488)
  - [烟雾浓度传感器](https://detail.tmall.com/item.htm?id=41265308241)
  - [焊锡丝](https://detail.tmall.com/item.htm?id=41210055060)
  - 
  - [红外接收模块](https://item.taobao.com/item.htm?id=15205557249)
  - [逻辑分析仪](https://item.taobao.com/item.htm?id=549493689933)
  - [红外发射模块](https://item.taobao.com/item.htm?id=26096060107)
  - 
  - [数码舵机](https://item.taobao.com/item.htm?id=539371872298)
  - [电池盒](https://item.taobao.com/item.htm?id=552641395246)
  - [面包板](https://detail.tmall.com/item.htm?id=41227942545)
  - [胶枪](https://detail.tmall.com/item.htm?id=520307105479)
  - [缝纫线](https://item.taobao.com/item.htm?id=44306627361)
- 软件
  - 客户端
    - [XAMPP](https://www.apachefriends.org/zh_cn/index.html)
    - [花生壳](https://hsk.oray.com/price/#personal)
    - [NATAPP](https://natapp.cn/)
  - 库
    - [workerman for win](https://github.com/walkor/workerman-for-win)
    - [PHPMailer](https://github.com/PHPMailer/PHPMailer)
    - 
    - [Arduino-IRremote](https://github.com/z3t0/Arduino-IRremote)
    - [BH1750](https://github.com/claws/BH1750)
    - [DHTstable](https://github.com/RobTillaart/Arduino/tree/master/libraries/DHTstable)

***
## 2.教程
&emsp;&emsp;说明:&emsp;此教程主要面向&emsp;对Web涉猎较少的&emsp;Arduino爱好者

&emsp;&emsp;step1&emsp;点击右上角绿色的`Clone or download`，然后选择`Download ZIP`，将所得到的`smartHome-master.zip`解压至桌面

&emsp;&emsp;step2&emsp;申请微信接口测试号，非程序员请戳 [weixin_sandbox.md](weixin_sandbox.md)

&emsp;&emsp;step3&emsp;将`Desktop/smartHome-master/src/server/微信菜单.txt`粘贴至[微信公众平台接口调试工具](https://mp.weixin.qq.com/debug/)，填写好之后单击`检查问题`；在浏览器中输入`localhost:8000/phpmyadmin/`，将`Desktop/smartHome-master/src/server/createTableAndUser.sql`粘贴至`phpMyAdmin`的SQL查询窗口并执行

<div align="center"><img src="/img/创建微信菜单.png" width=800px/></div>
<br/>
<div align="center"><img src="/img/创建数据表.png" width=800px/></div>
<br/>

&emsp;&emsp;step4&emsp;将`Desktop/smartHome-master/src/server/PHPMailer/`文件夹和`Desktop/smartHome-master/src/server/lpx_email.php`剪切至`C:/xampp/htdocs/`下，修改`lpx_email.php`中的三个define

&emsp;&emsp;step5&emsp;修改`Desktop/smartHome-master/src/arduino_light/arduinoGree.h`中的四个数组，具体请戳[Saleae.md](Saleae.md)

&emsp;&emsp;step6&emsp;安装[Arduino IDE](https://www.arduino.cc/en/Main/Software)，进行配置使其适用于ESP-01的编程，具体请戳[IDE_Conf.md](IDE_Conf.md)

&emsp;&emsp;step7&emsp;开通`花生壳内网穿透服务体验版`，非程序员请戳[hsk.md](hsk.md)

&emsp;&emsp;step8&emsp;将`arduino_light.ino、arduino_door.ino`和`esp8266_light.ino、esp8266_door.ino`分别烧入两块Arduino和两块ESP-01中

&emsp;&emsp;step9&emsp;改造插座

<div align="center"><img src="/img/改造插座.png" width=550px/></div>
<br/>

&emsp;&emsp;step10&emsp;接线

<div align="center"><img src="/img/总接线图1.png" width=550px/></div>
<br/>
<div align="center"><img src="/img/总接线图2.png" width=550px/></div>
<br/>
<div align="center"><img src="/img/硬件终端1.png" width=550px/></div>
<br/>
<div align="center"><img src="/img/硬件终端2.png" width=550pxx/></div>
<br/>

&emsp;&emsp;step11&emsp;确保XAMPP、花生壳、NATAPP的客户端均在运行，双击`Desktop/smartHome-master/src/server/start_8100.bat`和`Desktop/smartHome-master/src/server/start_8200.bat`

&emsp;&emsp;step12&emsp;再次打开[微信公众平台接口测试号](http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login)并登录，扫描二维码关注公众号，在微信上申请家庭成员认证

&emsp;&emsp;step13&emsp;在浏览器中输入`localhost:8000/phpmyadmin`，打开`db_arduino`数据库下的`family_member`表，可以看到有一条新的记录，将`pass`字段的值修改为`1`，将`openid`字段的值复制到`C:/xampp/htdocs/arduino/weixin.php`的`is_administrator`函数中

&emsp;&emsp;step14&emsp;Have fun!

***
## 3.后记
- 参考
  - [Arduino开源智能家居04《插座开关》](https://www.arduino.cn/thread-6550-1-3.html)
  - [没带钥匙？手机遥控开宿舍之门](https://www.jianshu.com/p/f436e696f3bd)
  - 
  - [手机控制esp8266控制arduino上的led灯](http://www.zhongbest.com/2017/01/03/%e6%89%8b%e6%9c%ba%e6%8e%a7%e5%88%b6esp8266%e6%8e%a7%e5%88%b6arduino%e4%b8%8a%e7%9a%84led%e7%81%af/)
  - [esp8266-01无线模块的arduino烧写方式](http://www.zhongbest.com/2017/01/02/esp8266-01%e6%97%a0%e7%ba%bf%e6%a8%a1%e5%9d%97%e7%9a%84arduino%e7%83%a7%e5%86%99%e6%96%b9%e5%bc%8f/) 
  - 
  - [微信开发如何做本地调试？](https://www.zhihu.com/question/25456655)
  - [微信公众平台接口开发之PHP版](https://blog.csdn.net/websites/article/details/19291915)
  - [微信公众平台如何创建自定义菜单](https://jingyan.baidu.com/article/6525d4b1376613ac7d2e94f8.html)
  - [小题大做系列Ⅱ(PM2.5微信控)](https://www.arduino.cn/thread-18326-1-1.html)
  - 
  - [Arduino进阶必看资料——将数据放在flash中](https://www.arduino.cn/thread-3612-1-1.html)
  - [315Mhz模块传输代替315Mhz遥控器](http://www.geek-workshop.com/thread-5258-1-1.html)
- 其它
  - 本人非计算机、软工专业，毕业设计题目与智能家居相关，主要用到了PHP和Arduino。在查阅资料的过程中，发现网上资源大多零散、支吾、费解，故决定写篇详细的教程以及放出代码，供有需要的同学参考。教程、编码中的不足之处欢迎issue
