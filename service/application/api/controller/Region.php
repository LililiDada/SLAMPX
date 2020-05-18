<?php

namespace app\api\controller;

use app\common\controller\BaseController;
use app\common\model\Region as RegionModel;
use app\common\validate\RegionValidate;

use think\Controller;
use think\Request;

class Region extends BaseController
{
    // 添加区域
    public function addRegion()
    {
        (new RegionValidate())->goCheck('add');
        $list = (new RegionModel())->addRegion();
        return self::showResCode('添加成功',$list);
    }

    // 获取区域列表
    public function getRegion()
    {
        (new RegionValidate())->goCheck('list');
        $list = (new RegionModel())->getRegion();
        return self::showResCode('获取成功',['list'=>$list]);
    }

    public function allRegion()
    {
        $list = (new RegionModel())->allRegion();
        return self::showResCode('获取成功',['list'=>$list]);
    }

    // 删除区域
    public function deleteRegion()
    {
        (new RegionValidate())->goCheck('delete');
        $list = (new RegionModel())->deleteRegion();
        return self::showResCodeWithOutData('删除成功');
    }

    
}
