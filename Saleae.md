&emsp;&emsp;step1&emsp;

&emsp;&emsp;&emsp;&emsp;&emsp;逻辑分析仪：CH1接红外接收模块的S，GND接Arduino的GND，USB口接电脑

&emsp;&emsp;&emsp;&emsp;&emsp;红外接收模块：+接Arduino的3V3，-接Arduino的GND

<div align="center"><img src="/img/逻辑分析仪.png" width=300px/></div>
<br/>

&emsp;&emsp;step2&emsp;将Saleae Logic的Channel 0设置为低电平触发，单击绿色的"Start"

<div align="center"><img src="/img/将Saleae Logic的Channel 0设置为低电平触发.png" width=800px/></div>
<br/>

&emsp;&emsp;step3&emsp;让遥控器对准红外接收器并按下任一按键，录制结果如下

<div align="center"><img src="/img/录制结果.png" width=800px/></div>
<br/>

&emsp;&emsp;step4&emsp;让`Timing Marker Pair`面板的A1对准第0秒，让A2对准最后一个上升沿。单击`Option`，选择`Export data`，在弹出的`Data Export`对话框中配置如下，单击`Export`

<div align="center"><img src="/img/设置Timing Marker Pair.png" width=800px/></div>
<div align="center"><img src="/img/Data Export对话框.png" width=350px/></div>
<br/>

&emsp;&emsp;step5&emsp;打开上一步导出的CSV文件，在C2单元格中输入`=INT((A3-A2)*1000*1000)`，双击C2单元格的右下角以自动填充第C列余下的单元格,则第C列就代表`闪烁过程中每次亮/暗的持续时间`，将第C列整理成一个数组即可

<div align="center"><img src="/img/对CSV文件进行处理.png" width=350px/></div>
<br/>

&emsp;&emsp;step6&emsp;返回[README.md](README.md)
