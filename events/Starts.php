<?php
namespace sunnnnn\wm\events;

use \Workerman\Worker;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \GatewayWorker\Register;

class Starts{
	protected $frame = ['tp5'];
	protected $cfg;
	
	public function __construct($frame, $config = []){
		$frame = empty($frame) ? 'tp5' : trim($frame);
		if(!in_array($frame, $this->frame)){
			exit('this frame is not support !');
		}
		
		if(empty($config)){
			switch($frame){
				case 'tp5':
					$this->cfg = config('workerman');
					break;
				default:$this->cfg = '';
			}
		}else{
			$this->cfg = $config;
		}
		
		if(empty($this->cfg)){
			exit('Please set the configuration file <param: workerman>!');
		}
	}
	
	public function startRegister(){
		// register 必须是text协议
		$register = new Register('text://'.$this->cfg['register_address']);
		Worker::runAll();
	}
	
	public function startGateway(){
		// gateway 进程，这里使用Text协议，可以用telnet测试
		$gateway = new Gateway($this->cfg['gateway_socket']);
		// gateway名称，status方便查看
		$gateway->name = $this->cfg['gateway_name'];
		// gateway进程数
		$gateway->count = $this->cfg['gateway_count'];
		// 本机ip，分布式部署时使用内网ip
		$gateway->lanIp = $this->cfg['gateway_lanIp'];
		// 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
		// 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
		$gateway->startPort = $this->cfg['gateway_startPort'];
		// 服务注册地址
		$gateway->registerAddress = $this->cfg['register_address'];
		// 心跳间隔,0为不发送心跳
		$gateway->pingInterval = $this->cfg['gateway_pingInterval'];
		//客户端连续$pingNotResponseLimit次$pingInterval时间内不回应心跳则断开链接。 
		//如果设置为0代表客户端不用发送回应数据，即通过TCP层面检测连接的连通性（极端情况至少10分钟才能检测到）
		$gateway->pingNotResponseLimit = $this->cfg['gateway_pingNotResponseLimit'];
		// 心跳数据
		$gateway->pingData = '{"type":"ping"}';
		Worker::runAll();
	}
	
	public function startBusiness(){
		// bussinessWorker 进程
		$worker = new BusinessWorker();
		// worker名称
		$worker->name = $this->cfg['business_name'];
		// bussinessWorker进程数量
		$worker->count = $this->cfg['business_count'];
		// 服务注册地址
		$worker->registerAddress = $this->cfg['register_address'];
		Worker::runAll();
	}
}