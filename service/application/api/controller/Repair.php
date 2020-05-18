<?php

namespace app\api\controller;
use app\common\controller\BaseController;
use app\common\model\Repair as RepairModel;
use app\common\validate\RepairValidate;
use think\Request;

class Repair extends BaseController
{
    // 添加故障
    public function addRepair()
    {
        (new RepairValidate())->goCheck('add');
        (new RepairModel())->addRepair();
        return self::showResCodeWithOutData('添加成功');
    }

    // 获取故障列表
    public function getRepair()
    {
        (new RepairValidate())->goCheck('get');
        $list = (new RepairModel())->getRepair();
        return self::showResCode('获取成功',['list'=>$list]);
    }

    // 获取设备五天一次的故障数量
    public function statisNum()
    {
        $list = (new RepairModel())->statisNum();
        return self::showResCode('获取成功',$list);
    }

    // 历史故障，紧急维修、日常维修的数量
    public function repairNum()
    {
        $list = (new RepairModel())->repairNum();
        return self::showResCode('获取成功',$list);
    }
}
