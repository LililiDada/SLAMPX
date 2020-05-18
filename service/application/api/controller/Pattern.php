<?php

namespace app\api\controller;

use app\common\controller\BaseController;
use app\common\model\Pattern as PatternModel;
use think\Request;

class Pattern extends BaseController
{
    
    public function index()
    {
        $list = (new PatternModel())->getPattern();
        return self::showResCode('获取成功',['list'=>$list]);
    }
   
}
