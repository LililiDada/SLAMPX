<?php

namespace app\api\controller;
use app\common\controller\BaseController;
use app\common\model\Mode as ModeModel;
use app\common\validate\ModeValidate;
use think\console\command\Lists;
use think\Request;

class Mode extends BaseController
{
    public function add()
    {
        (new ModeValidate())->goCheck('add');
        $id = (new ModeModel())->add();
        return self::showResCode('添加成功',['id'=>$id]);
    }

    public function delete()
    {
        (new ModeValidate())->goCheck('delete');
        $list = (new ModeModel())->delete();
        return self::showResCodeWithOutData('删除成功');
    }

    public function getList()
    {
       (new ModeValidate())->goCheck('list');
       $list = (new ModeModel())->getList();
       return self::showResCode('获取成功',['list' => $list]);
    }
}
