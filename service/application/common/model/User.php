<?php

namespace app\common\model;

use think\facade\Cache;
use think\Model;

class User extends Model
{
    // 自动写入时间
    protected $autoWriteTimestamp = true;

    // 关联设备表
    public function device()
    {
        return $this->hasMany('device','user_id');
    }
    // 关联故障表
    public function repair()
    {
        return $this->hasMany('repair','user_id');
    }
    // 注册账号   
    public function register()
    {
        $params = request()->param();
        // 判断用户是否存在
        $user = $this->isExist(['username'=>$params['username']]);
        if($user) TApiException('用户已存在',20001);
        $password = password_hash($params['password'],PASSWORD_DEFAULT);
        $user = self::create([
            'username' => $params['username'],
            'password' => $password
        ]);
        if(!$user) TApiException('添加失败');
        return $user;
    }

    // 判断用户是否存在
    public function isExist($arr=[])
    {
        if(!is_array($arr)) return false;
        if(array_key_exists('username',$arr)){
            $user = $this->where('username',$arr['username'])->find();
            return $user;
        }
    }

    // 用户是否被禁用
    public function checkStatus($arr)
    {
        $status = $arr['status'];
        if($status==0) TApiException('该用户已被禁用',20003);
        return $arr;
    }


    // 登录
    public function login()
    {
        $params = request()->param();
        // 用户是否存在
        $user = $this->isExist(['username'=>$params['username']]);
        if(!$user)  TApiException('该用户不存在',20002,200);
        // 用户是否被禁用
        $this->checkStatus($user->toArray());
        // 检查密码
        $this->checkPassword($params['password'],$user->password);
        // 登录成功，返回token和用户信息
        $userarr = $user->toArray();
        // halt($user->with([
        //     'scene'=> function($query){
        //         return $query->with('adjust')->find();
        //     }
        // ]));
        $scene = \Db::table('scene_adjust')
                    ->alias('a')
                    ->where('mode_id',$userarr['scene_id'])
                    ->join('adjust b','a.adjust_id = b.id')
                    // ->hidden(['create_time','mode_id',''])
                    ->select();
        $plain = \Db::table('scene_adjust')
                    ->alias('a')
                    ->where('mode_id',$userarr['plain_id'])
                    ->join('adjust b','a.adjust_id = b.id')
                    // ->hidden(['create_time','mode_id',''])
                    ->select();
        $userarr['scene']=$scene;
        $userarr['plain']=$plain;
        $userarr['token']=$this->CreateSaveToken($user->toArray());

        return $userarr;
    }

    // 生成并保存token
    public function CreateSaveToken($arr=[])
    {
        // 生成token
        $token = sha1(md5(uniqid(md5(microtime(true)),true)));
        $arr['token'] = $token;
        // 登录过期时间
        $expire = config('api.token_expire');
        // 保存在缓存中
        if(!Cache::set($token,$arr,$expire)) TApiException();
        // 返回token
        return $token;
        
    }

    // 验证密码
    public function checkPassword($password,$hash)
    {
        if(!password_verify($password,$hash)) TApiException('密码错误',20004,200);
        return true;
    }

    // 绑定
    public function bind($id,$name,$value)
    {
        $device = $this->get($id);
        $device -> $name = $value;
        $device -> save();
        if(!$device) TApiException('添加失败',30002);
        return true;
    }

    // 绑定默认场景模式
    public function bindScene()
    {
        $param = request()->param();
        $this->bind(request()->userId,'scene_id', $param['scene_id']);
        return true;
    }

    // 绑定默认普通模式
    public function bindPlain()
    {
        $param = request()->param();
        $this->bind(request()->userId,'plain_id', $param['plain_id']); 
        return true;
    }

    // 绑定当前的开关模式ID
    public function bindMode()
    {
        $param = request()->param();
        $this->bind(request()->userId,'mode_id', $param['mode_id']); 
        return true;
    }

    // 数据统计页面获取亮灯率，故障数量，开关时间
    public function getHistory()
    {
        $userId  = request()->userId;
        $user = $this->get($userId);
        $data = array();
        // 当前用户控制模式ID,mode为0时，用户调节区域是节能模式
        $data['mode']  = $user['mode_id'];
        // 获取当前用户在线设备数量
        $online  = $this->withCount([
                        'device' =>function($query) {
                            return $query->where('online',1);
                        },
                    ])->where('id',$userId)->find();
        $data['online']  =  $online['device_count'];
        // 获取当前用户的设备数量
        $count  =  $this->withCount('device','id')->where('id',$userId)->find();
        $data['count'] = $count['device_count'];
        // 获取当前用户该月的设备故障数量
        $j = date("t"); //获取当前月份天数
        $start_time = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
        $end_time  = strtotime(date('Y-m-'.$j));
        $fault = $this->withCount([
            'repair'=>function($query) use($start_time,$end_time){
                $query->where('create_time', 'between', [$start_time,$end_time]);
            }
        ])->where('id',$userId)->find();
        $data['fault'] = empty($fault['repair_count'])?0:$fault['repair_count'];
        $data['switch'] = \Db::table('scene_adjust')
                            ->alias('a')
                            ->where('mode_id',$user['mode_id'])
                            ->join('adjust b','a.adjust_id = b.id')
                            ->select();
        return $data;
    }

     // 数据统计页面获取区域亮灯率，故障数量，开关时间
     public function regionHistory()
     {
         $userId  = request()->userId;
         $user = $this->get($userId);
         $param = request()->param();
         $regionId = $param['region_id'];
         $data = array();
         // 当前用户控制模式ID,mode为0时，用户调节区域是节能模式
         $data['mode']  = $user['mode_id'];
         // 获取当前用户在线设备数量
         $online  = $this->withCount([
                         'device' =>function($query) use($regionId) {
                             return $query->where(['online'=>1,'region_id'=>$regionId]);
                         },
                     ])->where('id',$userId)->find();
         $data['online']  =  $online['device_count'];
         // 获取当前用户的设备数量
        //  $count  =  $this->withCount('device','id')->where('id',$userId)->find();
         $count  =  $this->withCount([
            'device' =>function($query) use($regionId) {
                return $query->where(['region_id'=>$regionId]);
            },
         ])->where('id',$userId)->find();
         $data['count'] = $count['device_count'];
         // 获取当前用户该月的设备故障数量
         $j = date("t"); //获取当前月份天数
         $start_time = strtotime(date('Y-m-01'));  //获取本月第一天时间戳
         $end_time  = strtotime(date('Y-m-'.$j));
        //  $fault = $this->withCount([
        //     'repair'  => function($query) use($regionId){
        //          return $query->field('id','device_id')->withCount([
        //              'device'=>function($query) use($regionId){
                         
        //                 return $query->where(['region_id'=>$regionId]);
        //              }
        //         ]);
        //     }
        //  ])->where('id',$userId)->where('create_time','between', [$start_time,$end_time])->find();

        $fault = \Db::table('device')
                    ->alias('d')
                    ->where('region_id',$regionId)
                    ->join('repair r','d.id=r.device_id')
                    ->where('r.create_time', 'between', [$start_time,$end_time])
                    ->count();

        //  $data['fault'] = empty($fault['repair_count'])?0:$fault['repair_count'];
        $data['fault'] = $fault;
        $data['switch'] = \Db::table('scene_adjust')
        ->alias('a')
        ->where('mode_id',$user['mode_id'])
        ->join('adjust b','a.adjust_id = b.id')
        ->select();
         return $data;
     }
}
