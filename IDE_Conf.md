&emsp;&emsp;step1&emsp;打开Arduino IDE，单击菜单栏中的`文件>首选项`，在弹出的对话框的`附加开发板管理器网址`中填写`http://arduino.esp8266.com/stable/package_esp8266com_index.json`，单击`好`

&emsp;&emsp;step2&emsp;单击菜单栏中的`工具>开发板>开发板管理器`，在弹出的对话框中输入`esp8266`进行搜索，单击搜索结果中的`安装`

&emsp;&emsp;step3&emsp;单击菜单栏中的`工具`，配置如下，并编写代码

<div align="center"><img src="/img/编写代码前的准备.png" width=300px/></div>
<br/>

&emsp;&emsp;step4&emsp;安装PL2303的驱动程序

&emsp;&emsp;step5&emsp;将ESP-01、PL2303按下图接线，在单击菜单栏中的`项目>上传`后，立刻将PL2303与电脑的USB口相连，即可将程序烧录到ESP-01中

<div align="center"><img src="/img/ESP-01模块在烧录时的接线图.png" width=400px/></div>
<br/>

&emsp;&emsp;step6&emsp;返回[README.md](README.md)
