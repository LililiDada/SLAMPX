<?php

namespace app\api\controller;
use app\common\controller\BaseController;
use app\common\model\Adjust as AdjustModel;
use app\common\validate\AdjustValidate;
use think\Request;

class Adjust extends BaseController
{
    // 添加亮度字符串
    public function create()
    {
       (new AdjustValidate())->goCheck('create');
       $id = (new AdjustModel())->add();
       return self::showResCode('添加成功',['id'=>$id]);
    }

    // 删除亮度时间段
    public function delete()
    {
        (new AdjustValidate())->goCheck('delete');
        (new AdjustModel())->delete();
        return self::showResCodeWithOutData('删除成功');
    }

    // 批量删除亮度时间段
    public function batchDelete()
    {
        (new AdjustValidate())->goCheck('batch');
        (new AdjustModel())->batchDelete();
        return self::showResCodeWithOutData('删除成功');
    }

    public function alter()
    {
        (new AdjustValidate())->goCheck('alter');
        $list = (new AdjustModel())->alter();
        return self::showResCodeWithOutData('修改成功');
    }
}
