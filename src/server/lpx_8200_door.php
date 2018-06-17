<?php
	require_once './lib_workerman/Autoloader.php';
	use \Workerman\Lib\Timer;	
	use Workerman\Worker;
	
	$worker=new Worker("tcp://0.0.0.0:8200");	
	$worker->onWorkerStart=function($worker){
		global $conn;
		$conn=new \Workerman\MySQL\Connection('localhost','3306','door','','db_arduino');
		
		Timer::add(27,function(){//每隔27s发送一次心跳
			global $worker;
			foreach($worker->connections as $esp8266)
			{	
				$esp8266->send('heart#!');
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
				if(strstr($command,'30')||strstr($command,'31'))//本服务器只处理开、关门
				{
					$seq_num=$result[0]['seq_num'];
					foreach($worker->connections as $esp8266)
					{
						$esp8266->send('command#'.$seq_num.'#'.$command.'!');
					}
				}
				else
					;
			}
		});
		
	};
	$worker->onConnect=function($connection){
		echo "new connection from ip " . $connection->getRemoteIp() . "\n";
	};
	$worker->onMessage=function($connection,$data){
		echo $data."\n";
		/*
		** esp8266会发送2种数据
		** heart#!		心跳
		** command#100#ok!	seq_num为100的指令执行成功
		*/
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
