<?php
	require_once 'lpx_email.php';
	//微信测试号没有消息加密功能,所以,官方的wxBizMsgCrypt用不上,直接进行XML解析即可
	$postData=file_get_contents("php://input");
	
	//微信消息的XML格式参见
	//mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140453
	//mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140454
	$xml_tree=simplexml_load_string($postData);
	$my_id=$xml_tree->ToUserName;
	$user_id=$xml_tree->FromUserName;
	$msgType=$xml_tree->MsgType;
	
	if($msgType=='text')
	{	
		$msg=$xml_tree->Content;
		if (strstr($msg,'申请')) {handle_family(50,$user_id,explode('#',$msg)[1]);}
		if (is_family_member($user_id)){
			if(strstr($msg,'环境参数'))	handle_surround();
			if(strstr($msg,'关灯'))	handle_control(10);
			if(strstr($msg,'开灯')) handle_control(11);
			if(strstr($msg,'关风扇')) handle_control(20);
			if(strstr($msg,'开风扇')) handle_control(21);
			if(strstr($msg,'关门'))	handle_control(30);
			if(strstr($msg,'开门'))	handle_control(31);
			if(strstr($msg,'空调mode0')) handle_control(40);
			if(strstr($msg,'空调mode1')) handle_control(41);
			if(strstr($msg,'空调mode2')) handle_control(42);
			if(strstr($msg,'空调mode3')) handle_control(43);
			if (is_administrator($user_id)){
				if(strstr($msg,'授权')) handle_family(51,explode('#',$msg)[1],null);
				if(strstr($msg,'撤销')) handle_family(52,explode('#',$msg)[1],null);
				if(strstr($msg,'拉黑')) handle_family(53,explode('#',$msg)[1],null);
			}
			else return_to_weixin('请输入正确的指令');
		}
		else return_to_weixin("请先申请家庭成员认证，格式为:\n申请#你的姓名");
	}
	elseif($msgType=='event')
	{
		$click=$xml_tree->EventKey;
		if(strstr($click,'family')) return_to_weixin("申请格式为:\n申请#你的姓名");
		if(strstr($click,'help')) handle_help();
		if(is_family_member($user_id)){
			if(strstr($click,'doorOpen')) handle_control(31);
			if(strstr($click,'lightOn')) handle_control(11);
			if(strstr($click,'lightOff')) handle_control(10);
			if(strstr($click,'surround')) handle_surround();
		}
		else return_to_weixin("请先申请家庭成员认证，格式为:\n申请#你的姓名");
	}

	//身份验证,返回微信,帮助信息
	function is_administrator($openid){
		return $openid=='管理员的openid';
	}
	
	function is_family_member($openid){
		$servername='localhost';
		$username='family';
		$password='';
		$db='db_arduino';
		$conn=new mysqli($servername,$username,$password,$db);
		$sql="SET CHARACTER SET 'utf8'";//防止中文乱码
		$conn->query($sql);
		$sql="SET NAMES 'utf8'";//防止中文乱码
		$conn->query($sql);
		$sql="SELECT COUNT(*) FROM family_member WHERE black=FALSE AND pass=TRUE AND openid=?";
		$stmt=$conn->prepare($sql);
		$stmt->bind_param('s',$openid);
		$stmt->bind_result($count);
		$stmt->execute();
		$stmt->fetch();
		if ($count)
			return TRUE;
		else
			return FALSE;
	}
	
	function return_to_weixin($return_content)
	{
		global $my_id;
		global $user_id;
		$return_format=
		"<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
		</xml>";
		$ret=sprintf($return_format,$user_id,$my_id,time(),$return_content);
		echo $ret;
	}
	
	function handle_help(){
		$temp=	"欢迎使用，可输入的指令如下:\n\n申请#你的名字\n环境参数\n关灯\n开灯\n关风扇\n".
				"开风扇\n关门\n开门\n空调mode0(关机)\n空调mode1(开机+快冷)\n".
				"空调mode2(24℃+除湿+风速30%+扫风)\n空调mode3(26℃+送风+风速100%+扫风)\n".
				"注：控制硬件时，相邻的两次操作应间隔5秒\n";
		return_to_weixin($temp);
	}
	//End 身份验证,返回微信,帮助信息

	//授权,撤销,拉黑,申请加入
	function handle_family($code,$openid,$name){
		$servername='localhost';
		$username='family';
		$password='';
		$db='db_arduino';
		$conn=new mysqli($servername,$username,$password,$db);
		$sql="SET CHARACTER SET 'utf8'";//防止中文乱码
		$conn->query($sql);
		$sql="SET NAMES 'utf8'";//防止中文乱码
		$conn->query($sql);
		switch($code){
			case 50:
				$sql="SELECT COUNT(*) FROM family_member WHERE black=TRUE AND openid=?";
				$stmt=$conn->prepare($sql);
				$stmt->bind_param('s',$openid);
				$stmt->bind_result($count);
				$stmt->execute();
				$stmt->fetch();
				if ($count){
					return_to_weixin('你已被管理员列入黑名单，申请无效');
					break;
				}
				
				$conn->close();//重新连接,否则下面的bind_param()会报错
				$conn=new mysqli($servername,$username,$password,$db);
				$sql="SET CHARACTER SET 'utf8'";
				$conn->query($sql);
				$sql="SET NAMES 'utf8'";
				$conn->query($sql);
				
				$sql="SELECT COUNT(*) FROM family_member WHERE black=FALSE AND openid=?";
				$stmt=$conn->prepare($sql);
				$stmt->bind_param('s',$openid);
				$stmt->bind_result($count);
				$stmt->execute();
				$stmt->fetch();
				if ($count){
					return_to_weixin('请勿重复申请');
					break;
				}
				
				$conn->close();//重新连接,否则下面的bind_param()会报错
				$conn=new mysqli($servername,$username,$password,$db);
				$sql="SET CHARACTER SET 'utf8'";
				$conn->query($sql);
				$sql="SET NAMES 'utf8'";
				$conn->query($sql);
				
				$sql="INSERT INTO family_member (openid,name) VALUES(?,?)";
				$stmt=$conn->prepare($sql);
				$stmt->bind_param('ss',$openid,$name);
				$stmt->execute();
				lpx_email("openid为[ $openid ]的[ $name ]申请\"家庭成员认证\"");
				return_to_weixin("\"家庭成员认证\"请求已发出，等待管理员审核");
				break;
			case 51:
				$sql="UPDATE family_member SET pass=TRUE,black=FALSE WHERE openid=?";//预处理语句
				$stmt=$conn->prepare($sql);
				$stmt->bind_param("s",$openid);
				$stmt->execute();
				return_to_weixin('管理员你好，已授权此人');
				break;
			case 52:
				$sql="DELETE FROM family_member WHERE openid=?";
				$stmt=$conn->prepare($sql);
				$stmt->bind_param("s",$openid);
				$stmt->execute();
				return_to_weixin('管理员你好，已撤销此人');
				break;
			case 53:
				$sql="UPDATE family_member SET pass=FALSE,black=TRUE WHERE openid=?";//预处理语句
				$stmt=$conn->prepare($sql);
				$stmt->bind_param("s",$openid);
				$stmt->execute();			
				return_to_weixin('管理员你好，已拉黑此人');
				break;
		}
		$conn->close();
	}
	//End 授权,撤销,拉黑,申请加入

	//环境参数获取,IOT控制
	function handle_surround(){
		$servername='localhost';
		$username='family';
		$password='';
		$db='db_arduino';
		$conn=new mysqli($servername,$username,$password,$db);
		$sql="SELECT * FROM surround WHERE seq_num=(SELECT MAX(seq_num) FROM surround)";
		$result=$conn->query($sql);
		$virtualTable[0]=$result->fetch_assoc();
		$temp=  "最后更新：". $virtualTable[0]['upload_time'] ."\n".
				"温度：". $virtualTable[0]['dht11_t'] ."℃\n".
				"湿度：". $virtualTable[0]['dht11_h'] ."%\n".
				"烟雾：". $virtualTable[0]['mq2'] ."\n".
				"光线强度：". $virtualTable[0]['bh1750'] ."lux";
		return_to_weixin($temp);
		$conn->close();
	}
	
	function handle_control($code){
		$servername='localhost';
		$username='family';
		$password='';
		$db='db_arduino';
		$conn=new mysqli($servername,$username,$password,$db);
		$sql="INSERT INTO control (command) VALUES( $code )";
		$conn->query($sql);
		switch($code){
			case 10:
				return_to_weixin('ok 关灯');
				break;
			case 11:
				return_to_weixin('ok 开灯');
				break;
			case 20:
				return_to_weixin('ok 关风扇');
				break;
			case 21:
				return_to_weixin('ok 开风扇');
				break;
			case 30:
				return_to_weixin('ok 关门');
				break;
			case 31:
				return_to_weixin('ok 开门');
				break;
			case 40:
				return_to_weixin('ok 关机');
				break;
			case 41:
				return_to_weixin('ok 开机+快冷');
				break;	
			case 42:
				return_to_weixin('ok 24℃+除湿+风速30%+扫风');
				break;			
			case 43:
				return_to_weixin('ok 26℃+送风+风速100%+扫风');
				break;				
		}
		$conn->close();
	}
	//End 环境参数获取,IOT控制
?>
