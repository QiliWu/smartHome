#include "arduinoGree.h"

void arduinoGree::mode0()
{
	iAmIR.sendRaw(mode0_a,806,38);
}

void arduinoGree::mode1()
{
	iAmIR.sendRaw(mode1_a,786,38);//794是数组长度,38是载波频率
	/*
	** 使用了PROGMEM后，需要修改irSend.cpp中的sendRaw方法

	** if (i & 1)  space(buf[i]) ;
	** else        mark (buf[i]) ;

	** 被修改为

	** if (i & 1)  space(pgm_read_word(&buf[i])) ;
	** else        mark (pgm_read_word(&buf[i])) ;
	*/
}

void arduinoGree::mode2()
{
	iAmIR.sendRaw(mode2_a,805,38);
}

void arduinoGree::mode3()
{
	iAmIR.sendRaw(mode3_a,802,38);
}
