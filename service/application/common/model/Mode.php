<?php

namespace app\common\model;

use think\Model;

class Mode extends Model
{

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 关联adjust表
    public function adjust(){
        return $this->belongsToMany('Adjust','scene_adjust');
    }

    // 添加模式
    public function add()
    {
        $params = request()->param();
        $userId = request()->userId;
        $mode = $this->create([
            "user_id"      =>    $userId,
            "pattern_id"   =>    $params['pattern_id']
        ]);
        if(!$mode) TApiException();
        $params['modelist'] = json_decode($params['modelist'],true);
        $modelistLength = count($params['modelist']);
        if($modelistLength > 0){
            $mode->adjust()->attach($params['modelist'],['create_time'=>time()]);
        }
        $data = $this->with(['adjust'])->find($mode->id);
        return $data->id;
    }

    // 删除场景
    public function delete()
    {
        $param = request()->param();
        $list = $this->where('id',$param['id'])->find();
        $mode = $this->where('id',$list['id'])->delete();
        if(!$mode) TApiException('删除失败',50001,400);
        $adjust = $list->adjust;
        foreach ($adjust as $ur) {
            $adjustId = $ur['id'];
            $res = $list->adjust()->detach($ur);
            $a = \Db::table('adjust')->where('id',$ur['id'])->delete();
        }
        return;
    }

    public function getList()
    {
        $params = request()->param();
        $list = $this->with('adjust')->hidden(['adjust.pivot','create_time','adjust.create_time','user_id','pattern_id'])->where([
            'pattern_id'=>$params['pattern_id'],
            'user_id'   =>request()->userId
        ])->page($params['page'],$params['num'])->order('create_time','desc')->select();
        if(!$list) TApiException();
        return $list;
    }
}
