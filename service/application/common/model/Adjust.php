<?php

namespace app\common\model;

use think\Model;

class Adjust extends Model
{
     // 自动写入时间
     protected $autoWriteTimestamp = true;


    //添加亮度时间段
    public function add()
    {
        $param = request()->param();
        $list = $this::create([
            'start_time'  =>   $param['start_time'],
            'start_min'   =>   $param['start_min'],
            'end_time'    =>   $param['end_time'],
            'end_min'     =>   $param['end_min'],
            'light'       =>   $param['light'],
        ]);
        if(!$list) TApiException('添加失败',50001,400);
        return $list->id;
    }

    // 删除调节时间段
    public function delete()
    {
        $param = request()->param();
        $list = $this->where('id',$param['id'])->delete();
        if(!$list) TApiException('删除失败',50001,400);
        return $list;
    }

    public function batchDelete()
    {
        $params = request()->param();
        $params['adjustlist'] = json_decode($params['adjustlist'],true);
        $list = $this ->where('id','IN',$params['adjustlist'])-> delete();
        if(!$list) TApiException('删除失败',50001,400);
        return $list;
    }

    // 修改亮度时间段
    public function alter()
    {
        $params = request()->param();
        $params['alterlist'] = json_decode($params['alterlist'],true);
        $list = $this->where('id',$params['id'])->update($params['alterlist']);
        if(!$list) TApiException('修改失败',50002, 400);
    }
}


