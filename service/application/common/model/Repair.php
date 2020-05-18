<?php

namespace app\common\model;

use think\Model;

class Repair extends Model
{
    // 自动写入时间
    protected $autoWriteTimestamp = true;

    public function device()
    {
        return $this->belongsTo('Device');
    }

    public function addRepair()
    {
       $params = request()->param();
       $userId = request()->userId;
       $list = self::create([
            'fault'       =>   $params['fault'],
            'device_id'   =>   $params['device_id'],
            'status'      =>   $params['status'],
            'user_id'     =>   $userId,
       ]);
       if(!$list) TApiException('添加失败');
       return;
    }

    public function getRepair()
    {
        $params = request()->param();
        $userId = request()->userId;
        $num = array_key_exists('num',$params)?$params['num']:config('api.device_num');
        if($params['status'] == 2){
            $data = [
                'user_id'   =>  $userId
            ];
        }else{
            $data = [
                'status'    =>  $params['status'],
                'user_id'   =>  $userId
            ];
        }
        $list = $this->where($data)->with([
            'device' =>   function($query){
                $query->field('id,region_id,lamp_id,address')->with(['region'=>function($query){
                    $query->field('id,regionname');
                }]);
            }
        ])->page($params['page'],$num)->order('create_time','desc')->select();
        return $list;
    }

    // 获取设备七个五天一次的故障数量
    public function statisNum()
    {
        $userId = request()->userId;
        // 获取当前时间的时间戳
        $a=date("Y-m-d",time());
        // strtotime($a)为当前时间的零点时间
        $end = strtotime($a);
        $yData=array();
        for($i=0;$i<7;$i++)
        {
            $yData[] = $this->where('create_time', 'between', [$end-($i+1)*3600*24*5, $end-$i*3600*24*5])->where('user_id',$userId)->count(); //每隔五天赋值给数组
            
        }
        $start_time = strtotime(date("Y-m-d",time()));  //获取当前时间戳
        $xData = array();
        for($i=0;$i<7;$i++)
        {
            $xData[] = date('m-d',$start_time-$i*3600*24*5); //每隔五天赋值给数组
        }
        $data['yData'] = $yData;
        $data['xData'] = $xData;
        $data['max'] = max($yData);
        return $data;
    }

    // 历史故障，紧急维修、日常维修的数量
    public function repairNum()
    {
        $userId = request()->userId;
        $data = array();
        $data['history'] = $this->where('user_id',$userId)->count();
        $data['urgent'] = $this->where([
            'user_id'   =>  $userId,
            'status'    =>  0,
        ])->count();
        $data['daily'] = $this->where([
            'user_id'   =>  $userId,
            'status'    =>  1,
        ])->count();
        return $data;
    }
}
