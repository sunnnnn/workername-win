将GatewayWorker/Workerman 集成到php框架中（tp5，yii2） windows 版本
=================
#配置
config.php中放入配置信息：
'workerman' => [
		'register_address' => '0.0.0.0:1238', //注册ip以及端口
		'gateway_socket' => 'Websocket://0.0.0.0:8282', //协议，IP，端口
		'gateway_name' => 'gatewayTest', //自定义gateway名称
		'gateway_count' => '1', //gateway 进程数
		'gateway_lanIp' => '127.0.0.1', //本机ip，分布式部署时使用内网ip
		'gateway_startPort' => '4000',// 内部通讯起始端口
		'gateway_pingInterval' => '0',// 心跳间隔,0为不发送心跳
		'gateway_pingNotResponseLimit' => '0',//几秒内不回应心跳则断开链接,0表示不断开
		'gateway_registerAddress' => '127.0.0.1:1238', //服务注册地址
		'business_name' => 'businessTest',//自定义business名称
		'business_count' => '1',//business 进程数
		'business_registerAddress' => '127.0.0.1:1238', //服务注册地址
	],
========
#启动
新增Start.php 继承events\Starts类（或者不继承，用new一个Starts对象）
重写business函数：

use sunnnnn\workerman\Starts;
use \GatewayWorker\BusinessWorker;

class Start extends Starts{
	
	public function business(){
		$worker = new BusinessWorker();
		parent::setBusiness($worker);
	}
}

将start中的启动文件放入网站根目录，修改其中的路径，启动start（windows下需要启动三个文件）
========
#逻辑业务
新增MyEvent.php 继承events\Events类
重新onConnect 、onMessage、onClose 实现业务逻辑：

use sunnnnn\workerman\Events;
use \GatewayWorker\Lib\Gateway;

class MyEvent extends Events{
	
	public static function onConnect($client_id){}
	
   	public static function onMessage($client_id, $message){}
   
   	public static function onClose($client_id){}

}
======
其余请参考官方手册
http://www.workerman.net/gatewaydoc/

