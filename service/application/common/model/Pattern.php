<?php

namespace app\common\model;

use think\Model;

class Pattern extends Model
{
    public function getPattern()
    {
        $list = $this->where('status',1)->field(['create_time','status'],true)->select();
        return $list;
    }
    
}
