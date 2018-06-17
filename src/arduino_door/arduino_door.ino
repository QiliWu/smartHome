#include <Servo.h>

#define SERVO_PIN 10
Servo iAmServo;

void setup()
{
    Serial.begin(115200);
    while (!Serial)
		;
    iAmServo.attach(SERVO_PIN);
    iAmServo.write(0);//舵机上电后会自动转到90°处，此处先让其恢复到0°
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
    if(command.startsWith("command#"))
    {   //对command#100#31!进行分割
        int i=command.lastIndexOf("#")+1;
        int j=command.indexOf("!");
        int code=command.substring(i,j).toInt();
        switch(code)
        {
            case 30:
                close_door();
                break;
            case 31:
                open_door();
                break;			
        }
    }    
}

void close_door()
{
    for (int pos=180;pos>=0;--pos)//逆时针旋转
    {
        iAmServo.write(pos);
        delay(15);
    }
}

void open_door()
{
    for (int pos=0;pos<=180;++pos)//顺时针旋转
    {
        iAmServo.write(pos);
        delay(15);
    }
}
