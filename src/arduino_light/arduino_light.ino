#include <Wire.h>
#include "dht.h"
#include "BH1750.h"
#include "arduinoGree.h"

#define MQ2_PIN 0
#define DHT_PIN 8
#define LIGHT_PIN 9
#define FAN_PIN 10
dht iAmDHT;
BH1750 iAmBH1750;
arduinoGree iAmGree;//IRremote库规定：红外发射模块的signal接在Uno R3的D3引脚

void setup()
{
    Serial.begin(115200);
    while (!Serial)
		;
	Wire.begin();//初始化I2C总线，这一句是为BH1750准备的
    pinMode(LIGHT_PIN, OUTPUT);
    pinMode(FAN_PIN, OUTPUT);
	iAmBH1750.begin();
}

void loop()
{
    delay(369);//这一句必须加，否则每轮循环下来的command全为单个字符
    String command;
    while(Serial.available())
    {
        command+=char(Serial.read());
    }
    esp8266_say(command);     
}

void esp8266_say(String command)
{
    if(command=="sensor#!")
        all_sensor();
    if(command.startsWith("command#"))
    {   //对command#100#20!进行分割
        int i=command.lastIndexOf("#")+1;
        int j=command.indexOf("!");
        int code=command.substring(i,j).toInt();
        switch(code)
        {
            case 10:
                digitalWrite(LIGHT_PIN, LOW);//关灯
                break;
            case 11:
                digitalWrite(LIGHT_PIN, HIGH);//开灯
                break;
            case 20:
                digitalWrite(FAN_PIN, LOW);//关风扇
                break;
            case 21:
                digitalWrite(FAN_PIN, HIGH);//开风扇
                break;
			case 40:
				iAmGree.mode0();//关机
				break;
			case 41:
				iAmGree.mode1();//开机+快冷
				break;
			case 42:
				iAmGree.mode2();//24℃+除湿+风速30%+上下扫风
				break;
			case 43:
				iAmGree.mode3();//26℃+送风+风速100%+上下扫风
				break;				
        }
     }    
}

void all_sensor()
{
	if (iAmDHT.read11(DHT_PIN) == DHTLIB_OK)
	{
		String result="T=";
		result = result+iAmDHT.temperature+
				 "&H="+iAmDHT.humidity+
				 "&MQ2="+(analogRead(MQ2_PIN)/1023.0)+
				 "&BH1750="+iAmBH1750.readLightLevel()+"!";
		Serial.print(result);
	}
}
