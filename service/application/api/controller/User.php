<?php

namespace app\api\controller;
use app\common\controller\BaseController;
use think\Request;
use app\common\validate\UserValidate;
use app\common\model\User as UserModel;

class User extends BaseController
{
    // 登录
    public function login()
    {
 
        (new UserValidate())->goCheck('login');
        $data = (new UserModel())->login();
        return self::showResCode('登录成功',$data);

    }
    // 注册
    public function register()
    {
        (new UserValidate())->goCheck('register');
        $data = (new UserModel())->register();
        return self::showResCode("注册成功",$data);
    }

    // 绑定默认场景模式
    public function bindScene()
    {
        (new UserValidate())->goCheck('scene');
        (new UserModel())->bindScene();
        return self::showResCodeWithOutData('绑定成功');
    }

    // 绑定默认普通模式
    public function bindPlain()
    {
        (new UserValidate())->goCheck('plain');
        (new UserModel())->bindPlain();
        return self::showResCodeWithOutData('绑定成功');
    }

    // 绑定用户当前开关时间模式ID
    public function bindMode()
    {
        (new UserValidate())->goCheck('mode');
        (new UserModel())->bindMode();
        return self::showResCodeWithOutData('绑定成功');
    }

    // 数据统计页面获取亮灯率，故障数量，开关时间
    public function getHistory()
    {
        $list = (new UserModel())->getHistory();
        return self::showResCode('获取成功',$list);
    }

    // 数据统计页面获取区域亮灯率，故障数量，开关时间
    public function regionHistory()
    {
        (new UserValidate())->goCheck('region');
        $list = (new UserModel())->regionHistory();
        return self::showResCode('获取成功',$list);
    }
}
