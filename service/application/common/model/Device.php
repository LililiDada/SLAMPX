<?php

namespace app\common\model;

use think\facade\Cache;
use think\Model;

class Device extends Model
{

     // 自动写入时间
     protected $autoWriteTimestamp = true;
     
     public function region()
     {
         return $this->belongsTo('Region');
     }

    // 获取设备列表
    public function getDeviceList()
    {
        $userId = request()->userId;
        // $param = request()->param();
        // if(array_key_exists('page',$param)){
        //     return $this->where('user_id',$userId)->with([
        //         'region' => function($query){
        //             return $query->field('id,regionname');
        //         }
        //     ])->removeOption('region_id')->field(['create_time','user_id'],true)->page($param['page'],$param['num'])->select();
        // };
        $list = $this->where('user_id',$userId)->with([
            'region' => function($query){
                return $query->field('id,regionname');
            }
        ])->removeOption('region_id')->field('id,longitude,latitude,lamp_id,region_id,scene_id,plain_id,address,online,status')->order('create_time','desc')->select();
        $data['list'] = $list->toArray();
        // 不在线设备数量
        $onlineCount = $this->where([
            'user_id'  =>    $userId,
            'online'   =>    0,
        ])->count();
        // 故障设备数量
        $faultCount = $this->where([
            'user_id'  =>    $userId,
            'status'   =>    0,
        ])->count();
        // 正常设备数量
        $runCount = $this->where([
            'user_id'  =>    $userId,
            'status'   =>    1,
        ])->count();
        
        $data['onlineCount']  =  $onlineCount;
        $data['faultCount']   =  $faultCount;
        $data['runCount']     =  $runCount;
        // halt($data);
        return $data;
    }

    // 获取列表页面
    public function getDevice()
    {
        $userId = request()->userId;
        $param = request()->param();
        return $this->where('user_id',$userId)->with([
            'region' => function($query){
                return $query->field('id,regionname');
            }
        ])->removeOption('region_id')->field(['create_time','user_id'],true)->page($param['page'],$param['num'])->select();
    }

    // 百度地图转腾讯地图坐标
    function coorSwitch($bd_lon,$bd_lat){
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $bd_lon - 0.0065;
        $y = $bd_lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        // $data['gg_lon'] = $z * cos($theta);
        // $data['gg_lat'] = $z * sin($theta);
        $gg_lon = $z * cos($theta);
            $gg_lat = $z * sin($theta);
            // 保留小数点后六位
            $data['lon'] = round($gg_lon, 10);
            $data['lat'] = round($gg_lat, 10);
        return $data;
    }
    
    // 请求接口
    public function requeUrl($url)
    {
        $info = curl_init();
	    curl_setopt($info,CURLOPT_RETURNTRANSFER,1);  //如果把这行注释掉的话，就会直接输出
        curl_setopt($info,CURLOPT_HEADER,0);
        curl_setopt($info,CURLOPT_URL,$url);
        $output = curl_exec($info);
        curl_close($info);
        $info = json_decode($output);
        return $info;
    }

    // 请求腾讯地图api转换坐标
    public function webService($lon,$lat)
    {
        $data = 'locations'.'='.$lat.','.$lon.'&type=3&key='.config('api.key');
        $url = config('api.url').'?'.$data;
        $info = $this->requeUrl($url);
        if($info->status) return $this->coorSwitch($lon,$lat);
        $to['lon'] = $info->locations[0]->lng ;
        $to['lat'] = $info->locations[0]->lat ;
        return $to;
        
    }

    // 坐标获取地址
    public function geocoder($coor){
        $url = config('api.geocoder').'?'.'location'.'='.$coor['lat'].','.$coor['lon'].'&key='.config('api.key');
        $info = $this->requeUrl($url);
        if($info->status) return;
        $add = $info->result->address.$info->result->formatted_addresses->recommend;
        return $add;
    }

    // 添加设备
    public function addDevice()
    {
       $param = request()->param();
       $userId = request()->userId;
        // 判断该设备是否存在该用户下
    //    $dev = $this->where([
    //     'lamp_id'   =>  $param['lamp_id'],
    //     'user_id'   =>  $userId,
    //    ])->find();
    //     if($dev) TApiException('设备已存在',40002);
        // 百度地图转腾讯地图
       $isdevice = $this->where([
           'user_id'   =>   $userId,
           'lamp_id'   =>   $param['lamp_id'],
       ])->find();
       if($isdevice) TApiException('该设备已存在');
       $coor =$this->webService($param['longitude'],$param['latitude']);
       $address = $this->geocoder($coor);
       if(array_key_exists('region_id',$param)){
            $device = self::create([
                'longitude' =>  $coor['lon'],
                'latitude'  =>  $coor['lat'],
                'user_id'   =>  $userId,
                'region_id' =>  $param['region_id'],
                'lamp_id'   =>  $param['lamp_id'],
                'address'   =>   $address,
        		'imei'      =>  $param['imei']
            ]);
            return $device;
       }
       $device = self::create([
            'longitude'  =>  $coor['lon'],
            'latitude'   =>  $coor['lat'],
            'user_id'    =>  $userId,
            'lamp_id'    =>  $param['lamp_id'],
            'address'    =>  $address,
            'imei'       =>  $param['imei'] 
        ]);
        if(!$device) TApiException('添加失败');
        return $device;
    }

    // 删除设备
    public function deleteDevice()
    {
        $id = request()->param('id');
        $device = $this->where('id',$id)->delete();
        if(!$device) TApiException('删除失败',30001);
        return $device;
    }

    // 绑定区域
    public function bindRegion()
    {
        $param = request()->param();
        $this->bind($param['id'],'region_id', $param['region_id']);
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
        $this->bind($param['id'],'scene_id', $param['scene_id']);
        return true;
    }

    // 绑定默认普通模式
    public function bindPlain()
    {
        $param = request()->param();
        $this->bind($param['id'],'plain_id', $param['plain_id']); 
        return true;
    }

    // 获取某一区域下的全部设备
    public function regionDevice()
    {
        $userId = request()->userId;
        $param = request()->param();
        $num = array_key_exists('num',$param)?$param['num']:config('api.device_num');
        $list = $this->where([
            'user_id'       =>   $userId,
            'region_id'     =>   $param['region_id']
        ])->with([
            'region' => function($query){
                return $query->field('id,regionname');
            }
        ])->field(['create_time','user_id'],true)->page($param['page'],$num)->order('create_time','desc')->select();
        return $list;
    }

    // 获取当前设备
    public function deviceDetail()
    {
        $param = request()->param();
        
        $list = $this->with([
            'region' => function($query){
                return $query->field('id,regionname');
            }
        ])->field(['create_time','user_id'],true)->find($param['id'])->toArray();
         // ]));
         $scene = \Db::table('scene_adjust')
                        ->alias('a')
                        ->where('mode_id',$list['scene_id'])
                        ->join('adjust b','a.adjust_id = b.id')
                        // ->hidden(['create_time','mode_id',''])
                        ->select();
        $plain = \Db::table('scene_adjust')
                        ->alias('a')
                        ->where('mode_id',$list['plain_id'])
                        ->join('adjust b','a.adjust_id = b.id')
                        // ->hidden(['create_time','mode_id',''])
                        ->select();
        $list['scene'] = $scene;
        $list['plain'] = $plain;
        return $list;
    }

	// 获取设备ID
    public function getDeviceId()
    {
        // $pram = request()->param();
        // $regionid = json_decode($pram['regionid'],true);
        // if(in_array(0,$regionid)){
        //     return  $this->where([
        //         'user_id'       =>   request()->userId
        //     ])->column($pram['type']);
        // }
        // return $this->where([
        //     'user_id'       =>   request()->userId,
        //     'region_id'     =>   $regionid
        // ])->column($pram['type']);
        $pram = request()->param();
        $regionid = json_decode($pram['regionid'],true);
        if(in_array(0,$regionid)){
            $list =  $this->where([
                'user_id'       =>   request()->userId
            ])->column($pram['type']);
            return array_unique($list);
        }
        $list = $this->where([
            'user_id'       =>   request()->userId,
            'region_id'     =>   $regionid
        ])->column($pram['type']);
        return array_unique($list);
    }
    
    // 发送命令
   public function sendcommand()
    {
       $param = request()->param();
        // 区域ID
       $regionlist = explode(',',$param['regionlist']);
        // 模式ID，即要发送的亮度和数据等
       $scene_id = $param['scene_id'];
       $userId = request()->userId;
        // 权限之分
       if($userId == 2){
            $a = $this->where([
                'user_id'    => $userId,
                'region_id'  => $regionlist
            ])->field('imei,online')->find();
            $list[0]['imei'] = $a['imei'];
            $list[0]['online'] = $a['online'];
       }else{
            $list = $this->where([
                'user_id'    => $userId,
                'region_id'  => $regionlist
            ])->field('imei,online')->select();
       }
        // 根据模式Id查找具体的亮度和时间段
       $scene = \Db::table('scene_adjust')
                        ->alias('a')
                        ->where('mode_id',$scene_id)
                        ->join('adjust b','a.adjust_id = b.id')
                        // ->hidden(['create_time','mode_id',''])
                        ->select();
        // 拼接发送的时间段
        $scenelist = '';
        foreach($scene as $key => $value){
            if($key==count($scene)-2){
                   continue;
            }else{
                $scenelist.=$value['start_time'].':'.$value['start_min'].'-'.$value['end_time'].':'.$value['end_min'].'-';
            }
        }
        // 亮度数组
        $light = array_column($scene,'light');
        $lightlist = '';
        foreach($light as $key => $value){
            if($value<10) $value = '0'.$value;
            $lightlist.= $key==count($light)-1?$value:$value.'-';
        }
        // 设备亮度
       foreach($list as $value){
            // 取消缓存
            // $cacel = Cache::get($value['imei']);
            // if($cacel){
            //     $this->cacelOffline($cacel,$value['imei']);
            // }
			
            $mode = [
                'obj_id'          =>      config('api.mode_list.obj_id'),
                'obj_inst_id'     =>      config('api.mode_list.obj_inst_id'),
                'res_id'          =>      config('api.mode_list.res_id'),
            ];
            $plain_time = [
                'obj_id'          =>      config('api.time.obj_id'),
                'obj_inst_id'     =>      config('api.time.plain'),
                'res_id'          =>      config('api.time.res_id'),
                'value'           =>      $scenelist.$lightlist,
            ];
            // $plain_light = [
            //     'obj_id'          =>      config('api.light.obj_id'),
            //     'obj_inst_id'     =>      config('api.light.plain'),
            //     'res_id'          =>      config('api.light.res_id'),
            //     'value'           =>      $light[0],
            // ];
            $scene_time= [
                'obj_id'          =>      config('api.time.obj_id'),
                'obj_inst_id'     =>      config('api.time.scene'),
                'res_id'          =>      config('api.time.res_id'),
                'value'           =>      $scenelist.$lightlist,
            ];
            // $scene_1 = [
            //     'obj_id'          =>      config('api.light.obj_id'),
            //     'obj_inst_id'     =>      config('api.light.scene_1'),
            //     'res_id'          =>      config('api.light.res_id'),
            //     'value'           =>      $light[0],
            // ];
            // $scene_2 = [
            //     'obj_id'          =>      config('api.light.obj_id'),
            //     'obj_inst_id'     =>      config('api.light.scene_2'),
            //     'res_id'          =>      config('api.light.res_id'),
            //     'value'           =>      count($list)>3?$light[1]:0,
            // ];
            // $scene_3 = [
            //     'obj_id'          =>      config('api.light.obj_id'),
            //     'obj_inst_id'     =>      config('api.light.scene_3'),
            //     'res_id'          =>      config('api.light.res_id'),
            //     'value'           =>      count($list)>3?$light[2]:0,
            // ];
           if($value['online'] == 1){
                //   设备在线,发送即时命令
                // 正常模式
                if($param['mode'] == 1){
                    $mode['value']  =  1;
                    // 发送模式
                    // $this->request($value['imei'],$mode,true);
                    // 发送
                    $this->request($value['imei'],$plain_time,true);
                    // 发送亮度
                    // $this->request($value['imei'],$plain_light,true);
                }
                // 自适应模式
                else if($param['mode'] == 2){
                    $mode['value']  =  2;
                    // 发送模式
                    $this->request($value['imei'],$mode,true);
                }
                // 节能模式
                else{
                    $mode['value']  =  3;
                    // 发送模式
                    // $this->request($value['imei'],$mode,true);
                    // 发送时间段
                    $this->request($value['imei'],$scene_time,true);
                    // 发送亮度
                    // $this->request($value['imei'],$scene_1,true);
                    // $this->request($value['imei'],$scene_2,true);
                    // $this->request($value['imei'],$scene_3,true);
                }
           }else{
            //    设备不在线发送缓存命令
                $uuid =[];
                 // 正常模式
                 if($param['mode'] == 1){
                    $mode['value']  =  1;
                    // 发送模式
                    // $date = $this->request($value['imei'],$mode,false);
                    // array_push($uuid,$date['date']['uuid']);
                    // 发送时间段
                    $date1 = $this->request($value['imei'],$plain_time,false);
                    // array_push($uuid,$date1['date']['uuid']);
                    // 发送亮度
                    // $date2 = $this->request($value['imei'],$plain_light,false);
                    // array_push($uuid,$date2['date']['uuid']);
                }
                // 自适应模式
                else if($param['mode'] == 2){
                    $mode['value']  =  2;
                    // 发送模式
                    $date = $this->request($value['imei'],$mode,false);
                    // array_push($uuid,$date->data->uuid);
                }
                // 节能模式
                else{
                    $mode['value']  =  3;
                    // 发送模式
                    // $date = $this->request($value['imei'],$mode,false);
                    // array_push($uuid,$date['date']['uuid']);
                    // 发送时间段
                    $date1 = $this->request($value['imei'],$scene_time,false);
                    // array_push($uuid,$date1['date']['uuid']);
                    // 发送亮度
                    // $date2 = $this->request($value['imei'],$scene_1,false);
                    // array_push($uuid,$date2['date']['uuid']);
                    // $date3 = $this->request($value['imei'],$scene_2,false);
                    // array_push($uuid,$date3['date']['uuid']);
                    // $date4 = $this->request($value['imei'],$scene_3,false);
                    // array_push($uuid,$date4['date']['uuid']);
                }
                // Cache::set($value['imei'],$uuid,3600*24*3);
                // dump(Cache::get($value['imei']));
           }
         
       }
    }

    // 请求接口
    public function request($imei,$arr,$immediate)
    {
       
       if($immediate){
            $url                  =  config('api.onenetUrl');
            $url_data = array(
                'imei'            =>      $imei,
                'obj_id'          =>      $arr['obj_id'],
                'obj_inst_id'     =>      $arr['obj_inst_id'],
                'mode'            =>      config('api.mode'),
            );
       }else{
            $url                  =       config('api.offline_url');
            $url_data = array(
                'imei'            =>      $imei,
                'obj_id'          =>      $arr['obj_id'],
                'obj_inst_id'     =>      $arr['obj_inst_id'],
                'mode'            =>      config('api.mode'),
                'expired_time'    =>      date('Y-m-d\TH:i:s',strtotime("+3 day")),
            );
       }
       $url = $url.'?'.http_build_query($url_data, null, '&');
       $param = json_encode(array('data'=>[
           array(
               'res_id' => $arr['res_id'],
               'val'    => $arr['value']
           )
       ]));
       $headers = array(
           'content-type:application/json;charset=UTF-8',
           'api-key:'.config('api.api_key'),
           'Content-Type:application/x-www-from-urlencoded',
           'Content-Length:'.strlen($param)
       );
       // 初始化curl
       $ch = curl_init($url);
       curl_setopt_array($ch, array(
           // 不直接输出，返回到变量
           CURLOPT_RETURNTRANSFER => true,
           // 设置超时为60s，防止机器被大量超时请求卡死
           CURLOPT_TIMEOUT => 60
       ));
       curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers);
       // 支持POST请求
       if (!empty($param)) {
           curl_setopt_array($ch, array(
               CURLOPT_POST => true,
               // 设置POST参数
               CURLOPT_POSTFIELDS => $param
           ));
       }
       // 请求数据
       $data = curl_exec($ch);
       // 关闭请求
       curl_close($ch);
       // 对数据进行编码，方便前后端数据处理
       return json_decode($data);
    }

    // 取消缓存
    public function cacelOffline($cacel,$imei)
    {
        dump('数据缓存');
        dump($cacel);
        foreach($cacel as $value){
            $url = config('api.offline_cacel').$value.'?imei='.$imei;
            $headers = array(
                'content-type:application/json;charset=UTF-8',
                'api-key:'.config('api.api_key'),
                'Content-Type:application/x-www-from-urlencoded'
            );
            // 初始化curl
            $ch = curl_init($url);
            curl_setopt_array($ch, array(
                // 不直接输出，返回到变量
                CURLOPT_RETURNTRANSFER => true,
                // 设置超时为60s，防止机器被大量超时请求卡死
                CURLOPT_TIMEOUT => 60
            ));
            curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'put'); //设置请求方式
            // 请求数据
            $data = curl_exec($ch);
            dump($data);
            // 关闭请求
            curl_close($ch);
        }
    }
}
