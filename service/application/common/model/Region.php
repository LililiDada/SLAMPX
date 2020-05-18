<?php

namespace app\common\model;

use think\Model;

class Region extends Model
{
     // 自动写入时间
     protected $autoWriteTimestamp = true;

    // 添加区域
    public function addRegion()
    {
        $userId = request()->userId;
        $data = [
            'regionname' => request()->param('regionname'),
            'user_id'    => $userId
        ];
        if($this->where($data)->find()) TApiException('区域已存在',40001);
        $list = self::create($data);
        if(!$list)  TApiException('添加失败');
        return $list;
    }

    // 获取区域 
    public function getRegion()
    {
        $param = request()->param();
        $num = array_key_exists('num',$param)?$param['num']:config('api.region_num');
        $userId = request()->userId;
        return $this->where('user_id',$userId)->field(['id','regionname'])->page($param['page'],$num)->order('create_time','desc')->select();
    }

    // 全部
    public function allRegion(){
        $userId = request()->userId;
        return $this->where('user_id',$userId)->field(['id','regionname'])->order('create_time','desc')->select();
    }
     // 删除区域
     public function deleteRegion()
     {
         $id = request()->param('id');
         $region = $this->where('id',$id)->delete();
         if(!$region) TApiException('删除失败',30001);
         return $region;
     }

     
}
