<?php

namespace app\api\controller;

use app\common\controller\BaseController;
use app\common\model\Device as DeviceModel;
use app\common\validate\DeviceValidate;
use think\Request;

class Device extends BaseController
{
    // 获取全部灯泡列表
    public function index()
    {
        
        $list = (new DeviceModel())->getDeviceList();
        return self::showResCode('获取成功',['list'=>$list['list'],'onlineCount'=>$list['onlineCount'],'faultCount'=>$list['faultCount'],'runCount'=>$list['runCount']]);
    }

    // 添加设备
    public function addDevice()
    {
        (new DeviceValidate())->goCheck('add');
        $data = (new DeviceModel())->addDevice();
        return self::showResCodeWithOutData('添加成功');
    }

    // 删除设备
    public function deleteDevice()
    {
        (new DeviceValidate())->goCheck('delete');
        $list = (new DeviceModel())->deleteDevice();
        return self::showResCodeWithOutData('删除成功');
    }

    // 绑定区域
    public function bindRegion()
    {
        (new DeviceValidate())->goCheck('region');
        (new DeviceModel())->bindRegion();
        return self::showResCodeWithOutData('绑定成功');
    }

    // 绑定默认场景模式
    public function bindScene()
    {
        (new DeviceValidate())->goCheck('scene');
        (new DeviceModel())->bindScene();
        return self::showResCodeWithOutData('绑定成功');
    }

    // 绑定默认普通模式
    public function bindPlain()
    {
        (new DeviceValidate())->goCheck('plain');
        (new DeviceModel())->bindPlain();
        return self::showResCodeWithOutData('绑定成功');
    }
    
    // 指定区域下的设备列表
    public function regionDevice()
    {
        (new DeviceValidate())->goCheck('list');
        $list = (new DeviceModel())->regionDevice();
        return self::showResCode('获取成功',['list'=>$list]);
    }

    // 设备详情
    public function deviceDetail()
    {
        $list = (new DeviceModel())->deviceDetail();
        return self::showResCode('获取成功',['list'=>$list]);
    }

    // 获取可选页设备
    public function getDevice()
    {
        (new DeviceValidate())->goCheck('index');
        $list = (new DeviceModel())->getDevice();
        return self::showResCode('获取成功',['list'=>$list]);
    }
    
    // 获取设备ID
    public function getDeviceId(){
    	(new DeviceValidate())->goCheck('deviceid');
    	$list = (new DeviceModel())->getDeviceId();
        return self::showResCode('获取成功',$list);
    }
    
    // 发送命令
    public function sendcommand(){
        (new DeviceValidate())->goCheck('command');
        json('命令发送成功',200)->send();
    	(new DeviceModel())->sendcommand();
        // return self::showResCodeWithOutData('命令下发成功');
    }

}
