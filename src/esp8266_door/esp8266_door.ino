#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

//Wi-Fi名称及密码
char const* ssid="Wi-Fi名称";
char const* password="Wi-Fi密码";
//TCP服务器域名及端口
char const* host="www.club.club";
int const port=10153;
WiFiClient client;

void setup()
{
    Serial.begin(115200);//arduino、esp8266的波特率全设为115200
    delay(10);
    WiFi.begin(ssid,password);
    while(WiFi.status()!=WL_CONNECTED)
        delay(100);
}

void loop()
{   
    /*
    ** 监听8200端口的workerman可能会发来如下的2种指令 
    ** heart#!
    ** command#100#31!		其中100是seq_num,31是command
    */
    while(!client.connected())//处理重连
    {
        client.connect(host,port);
        delay(500);
    }
    String command;
    while(client.available())
    {
        command+=char(client.read());
        if(client.available()==0)
        {
            if(command.equals("heart#!"))
                client.print("heart#!");
            if(command.startsWith("command#"))
                control(command);
        }
    }
}

void control(String command)
{
    Serial.print(command);//向Arduino发出通知
    int i=command.lastIndexOf("#")+1;
    int j=command.indexOf("#");
    String seq_num=command.substring(i,j);
    String prefix="command";
    client.print(prefix+seq_num+"ok!");
}
