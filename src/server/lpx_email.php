<?php
require_once('./PHPMailer/SMTP.php');
require_once('./PHPMailer/PHPMailer.php');
require_once('./PHPMailer/Exception.php');
use PHPMailer\PHPMailer\PHPMailer;

function lpx_email($content){
	define("S","sender@163.com");//你的邮箱1
	define("P","abcdefg");//SMTP服务的授权码
	define("R","receiver@163.com");//你的邮箱2
	
	$mail=new PHPMailer();
	//$mail->SMTPDebug=1;
	$mail->isSMTP();
	$mail->SMTPAuth=true;
	$mail->Host='smtp.163.com';
	$mail->SMTPSecure='ssl';
	$mail->Port=465;
	$mail->CharSet='UTF-8';

	$mail->FromName=S;
	$mail->Username=S;
	$mail->From=S;
	$mail->Password=P;
	$mail->addAddress(R);
	$mail->Subject='申请加入';
	$mail->Body=$content;
	$mail->send();
}
?>
