<?php
	require_once './lib_workerman/Autoloader.php';
	use \Workerman\Lib\Timer;	
	use Workerman\Worker;
	
	$worker=new Worker("tcp://0.0.0.0:8100");	
	$worker->onWorkerStart=function($worker){
		global $conn;
		$conn=new \Workerman\MySQL\Connection('localhost','3306','light','','db_arduino');
		
		//注：27*151/60=67.95,也就是每经过1小时,heart#!和sensor#!就会连在一起发一次,但这无关紧要
		Timer::add(27,function(){//每隔27s发送一次心跳
			global $worker;
			foreach($worker->connections as $esp8266)
			{	
				$esp8266->send('heart#!');
			}
		});	
		Timer::add(151,function(){//每隔151s获取一次环境参数
			global $worker;
			foreach($worker->connections as $esp8266)
			{	
				$esp8266->send('sensor#!');
			}
		});
		Timer::add(1,function(){//每隔1s查询一下contorl表
			global $worker;
			global $conn;
			$sql=	"SELECT seq_num,command,status FROM control ".
					"WHERE seq_num=(SELECT MAX(seq_num) FROM control)";
			$result=$conn->query($sql);
			$status=$result[0]['status'];
			$command=$result[0]['command'];
			if (strstr($status,'ing'))
			{	
				if(strstr($command,'30')||strstr($command,'31'));//本服务器不处理开、关门
				else
				{
					$seq_num=$result[0]['seq_num'];
					foreach($worker->connections as $esp8266)
					{
						$esp8266->send('command#'.$seq_num.'#'.$command.'!');
					}
				}
			}
		});
		
	};
	$worker->onConnect=function($connection){
		echo "new connection from ip " . $connection->getRemoteIp() . "\n";
	};
	$worker->onMessage=function($connection,$data){
		echo $data."\n";
		/*
		** esp8266会发送3种数据
		** heart#!		心跳
		** command#100#ok!	seq_num为100的指令执行成功
		** sensor#T=24.00&H=95.00&MQ2=135!
		*/
		if(strstr($data,'sensor#'))//将环境参数写入数据库中
		{
			global $conn;
			$temp=explode('#',$data)[1];//得到T=24.00&H=95.00&MQ2=135&BH1750=773!
			$temp=explode('!',$temp)[0];//得到T=24.00&H=95.00&MQ2=135&BH1750=773
			
			$dht11_t=explode('&',$temp)[0];//得到T=24.00
			$dht11_h=explode('&',$temp)[1];//得到H=95.00
			$mq2=explode('&',$temp)[2];//得到MQ2=135
			$bh1750=explode('&',$temp)[3];//得到BH1750=773
			
			$dht11_t=explode('=',$dht11_t)[1];//得到24.00
			$dht11_h=explode('=',$dht11_h)[1];//得到95.00
			$mq2=explode('=',$mq2)[1];//得到135
			$bh1750=explode('=',$bh1750)[1];//得到773
			$sql="INSERT INTO surround (dht11_t,dht11_h,mq2,bh1750) ".
				"VALUES( $dht11_t , $dht11_h , $mq2 , $bh1750 )";
			$conn->query($sql);
		}
		if(strstr($data,'command#'))//修改seq_num为100的指令的状态
		{
			global $conn;
			$seq_num=explode('#',$data)[1];
			$sql="UPDATE control SET status='finished' WHERE seq_num= $seq_num ";
			$conn->query($sql);
		}
	};
	Worker::runAll();
?>
