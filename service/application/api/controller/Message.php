<?php

namespace app\api\controller;
use app\common\controller\BaseController;
use app\common\model\Message as MessageModel;
use app\common\validate\MessageValidate;
use GatewayWorker\Lib\Gateway;
use lib\util;
use think\Request;

class Message extends BaseController
{

	

    // 初始化registerAddress
    public function __construct()
    {
        Gateway::$registerAddress = config('api.registerAddress');
    }

    //  绑定上线
    public function bind(Request $request)
    {
        $userId = $request->userId;
        $client_id = $request->client_id;
        // 验证client_id合法性
        // if(!Gateway::isOnline($client_id)) return TApiException('clientId不合法');
        // 验证当前客户端是否已经绑定
        if(Gateway::getUidByClientId($client_id)) return TApiException('已经绑定');
        // 直接绑定
        Gateway::bindUid($request->client_id,$userId);
        // 返回成功
        return self::showResCode('绑定成功',['type'=>'bind','status'=>true]);
    }

    public function abutment(Request $request)
    {
        $Util = new Util();
        $raw_input = file_get_contents('php://input');
        $resolved_body = Util::resolveBody($raw_input);
        Util::l($resolved_body);
        if(request()->method() == 'GET'){
        	echo $resolved_body;
        }else {
            json($resolved_body,200)->send();
        }
        // $body=[
        //     "at"=>1586276209489,
        //     "imei"=>"869976031494728",
        //     "type"=>1,
        //     "ds_id"=>"3308_1_5900",
        //     "value"=>24.772976,
        //     "dev_id"=>587768654
        //     // "at"=>1586277120877,
        //     // "login_type"=>10,
        //     // "imei"=>"869976031494728",
        //     // "type"=>2,
        //     // "dev_id"=>587768654,
        //     // "status"=>1
        // ];
        $this->sendMessage($resolved_body);
        (new MessageModel())->abutment($resolved_body);
    }

    public function sendMessage($body)
    {
      // 判断是否为上下线
        if(array_key_exists('status',$body)){
            return;
        }
        // 根据设备ID查找改用户的ID
        $userId = \Db::table('device')->where('lamp_id',$body['dev_id'])->column('user_id');
        $userId = array_unique($userId);
        foreach($userId as $val){
            // 验证对方用户是否在线
            if (Gateway::isUidOnline($val)) {
                // 直接发送
                Gateway::sendToUid($val,json_encode($body));
            }
        }
        return;
    }
    
    // 获取具体相关设备的历史数据
    public function datastreams()
    {
        (new MessageValidate())->goCheck('datastreams');
        $list = (new MessageModel())->datastreams();
        return self::showResCode('获取成功',$list);
    }
    
    // 按月统计数据用电量
    public function monthStatis()
    {
        (new MessageValidate())->goCheck('monthStatis');
        $list = (new MessageModel())->monthStatis();
        return self::showResCode('获取成功',$list);
    }
}
