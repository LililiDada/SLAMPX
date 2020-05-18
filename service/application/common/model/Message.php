<?php

namespace app\common\model;

use think\Model;

class Message extends Model
{

     // 请求接口
     public function requeUrl($value,$imei)
     {
        $url                       =  config('api.onenetUrl');
        $url_data = array(
            'imei'            =>      $imei,
            'obj_id'          =>      config('api.obj_id'),
            'obj_inst_id'     =>      config('api.obj_inst_id'),
            'mode'            =>      config('api.mode'),
        );
        $url = $url.'?'.http_build_query($url_data, null, '&');
        $param = json_encode(array('data'=>[
            array(
                'res_id' => config('api.res_id'),
                'type'   => 2,
                'val'    => $value
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
        dump(curl_error($ch));
        // 关闭请求
        curl_close($ch);
        // 对数据进行编码，方便前后端数据处理
        return json_decode($data);
     }

    public function abutment($body)
    {
        // $post_data = array();
        // 设备上线
        if(array_key_exists('status',$body) && $body['status']== 1){
            // 校准时间
            \Db::name('device')
            	->where('lamp_id', $body['dev_id'])
        	    ->update(['online' => 1]);
            $this->requeUrl(time(),$body['imei']);
            sleep(5);
            $repos = $this->requeUrl(time(),$body['imei']);
            if($repos) return;
        }
        // 设备下线
        if(array_key_exists('status',$body) && $body['status']== 0){
            return \Db::name('device')
                        ->where('lamp_id', $body['dev_id'])
                        ->update(['online' => 0]);
        }
        if(array_key_exists('ds_id',$body) && $body['ds_id']== '3341_1_5528'){
            $userId = \Db::table('device')->where('lamp_id',$body['dev_id'])->column('user_id','id');
            if($body['value'] == 1)  $fault = '测量芯片温度过高';
            if($body['value'] == 2)  $fault = '开发板断电故障';
            // dump($userId);
            // 删除数组中相同的用户Id
            // $userId = array_unique($userId);
            if(isset($fault)){
                foreach($userId as $key => $val ){
                    \Db::table('repair')->insert([
                        'fault'       =>     $fault,
                        'device_id'   =>     $key,
                        'user_id'     =>     $val,
                        'status'      =>     0,
                        'create_time' =>     intval($body['at']/1000)
                    ]);
                }
            }
        }
        return $this->create([
            'dev_id'         =>  $body['dev_id'],
            'imei'           =>  $body['imei'],
            'ds_id'          =>  $body['ds_id'],
            'value'          =>  $body['value'],
            'create_time'    =>  intval($body['at']/1000)
        ]);

        //  if(array_key_exists('ds_id',$body) && $body['ds_id']== config('api.loa')){
        //     $loa = explode(":",$body['value']);
        //     $lon = $this->webService($loa[0],$loa[1]);
        //   \Db::name('device')
        //     ->where('lamp_id', $body['dev_id'])
        //     ->update([
        //         'longitude' => $lon['lon'],
        //         'latitude'  => $lon['lat']
        //     ]);
        // }
    }
     // 请求接口
     public function requestUrl($url)
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
         $info = $this->requestUrl($url);
         if($info->status) return $this->coorSwitch($lon,$lat);
         $to['lon'] = $info->locations[0]->lng ;
         $to['lat'] = $info->locations[0]->lat ;
         return $to;
         
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
            $data['lon'] = round($gg_lon, 6);
            $data['lat'] = round($gg_lat, 6);
        return $data;
    }
    
    // 获取具体相关设备的历史数据
    public function datastreams()
    {
        $param   =   request()->param();
        $ds_id   =   explode(",",$param['ds_id']);
        $dev_id  =   explode(",",$param['dev_id']);
        $time    =   explode(",",$param['time']);
        $time[1]  = date("Y-m-d",strtotime("+1 day",strtotime($time[1])));
        // $list = $this->where([
        //     ['dev_id','in',$dev_id],
        //     ['ds_id','in',$ds_id],
        //     ['create_time','between time',$time],
        //     // ['create_time','<',$time[1]],

        // ])->field('*')
        // ->withAttr('create_time',function ($value,$data) {
        //     return date("Y-m-d",$value);
        //    })->select();
        $list = $this->where([
            ['dev_id','in',$dev_id],
            ['ds_id','in',$ds_id],
            ['create_time','between time',$time],
        ])->field('sum(`value`) as sum,count(`value`) as count,avg(`value`) as avg,ds_id,create_time')->group("DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d'),ds_id")
        ->withAttr('create_time',function ($value,$data) {
            return date("Y-m-d",$value);
           })->select();
        // $res = [];
        // foreach ($list as $k => $v) {
        //     $res[$v['ds_id']]['ds_id'] = $v['ds_id'];
        //     $$res[$v['ds_id']]['list'][] = $v;
        // } 
        //将得到的数组用ds_id得到三维数组
        $res = array(); 
        foreach ($list as $v) {
            $key = $v['ds_id'];
            if(in_array($key, array_column($res, 'ds_id'))){
                continue;
            }
            $arr = array(); 
            foreach($list as $k){
                if($k['ds_id'] == $key){
                    array_push($arr,$k);
                }
            }
            $data['ds_id'] = $key;
            $data['list']  = $arr;
            array_push($res,$data);
        }
        return $res ;
    }
    
    // 按月统计月用电量
    public function monthStatis()
    {
        $value = '3331_0_5805';
        $param   =   request()->param();
        $dev_id  =   explode(",",$param['dev_id']);
        $time    =   explode(',',$param['time']);
        $time[1]  = date("Y-m-d",strtotime("+1 day",strtotime($time[1])));
        $list = $this->where([
            ['dev_id','in',$dev_id],
            ['ds_id','in',$value],
            ['create_time','between time',$time],
        ])->field('sum(`value`) as sum,FROM_UNIXTIME(create_time,"%Y-%m") as avg_time')->group("avg_time")
        ->select();
        return $list;
    }
}
