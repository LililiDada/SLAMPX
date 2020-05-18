<?php

return [
    // token过期时间
    'token_expire'        =>      3000,
    // 腾讯地图key 
    'key'                 =>      '334BZ-3A66Q-7BC55-G6BSN-DGI6F-NHBO6',
    // 腾讯地图坐标转换接口
    'url'                 =>      'https://apis.map.qq.com/ws/coord/v1/translate',
    // 腾讯地图坐标转地址
    'geocoder'            =>      'https://apis.map.qq.com/ws/geocoder/v1/',
    // 请求一次返回的设备个数
    'device_num'          =>      10,
    // 区域个数
    'region_num'          =>      10,
    // 长连接地址
    'registerAddress'     =>      '127.0.0.1:1238',
    // 经纬度
    'loa'                 =>      '3200_0_5750',
    // 时间校验的数据流id
    'obj_id'              =>      3333,
    // 时间校验的instance的id 
    'obj_inst_id'         =>      0,
    // 指定write操作的资源id
    'res_id'              =>      5506,
    // write的模式
    'mode'                =>      2,
    // 即时命令地址
    'onenetUrl'           =>      'https://api.heclouds.com/nbiot',
    // onenet的api_key
    'api_key'             =>      'lODwUzE3zIsoIXUd4dSRav=UZkM=',
    // 下发命令过期的时间
    'onenet_expire'       =>      0,
    // 缓存命令地址
    'offline_url'         =>      'https://api.heclouds.com/nbiot/offline',
    // 取消缓存命令地址
    'offline_cacel'       =>      'https://api.heclouds.com/nbiot/offline/cancel/',
    // 模式
    'mode_list'                =>    [
        'obj_id'          =>    3341,
        'obj_inst_id'     =>    0,
        'res_id'          =>    5528
    ],
    // 模式时间段
    'time'                =>    [
        'obj_id'          =>    3300,
        'scene'           =>    0,
        'plain'           =>    1,
        'res_id'          =>    5750
    ],
    // 亮度
    // 'light'               =>      [
    //     'obj_id'          =>    3311,
    //     'plain'           =>    0,
    //     'scene_1'         =>    1,
    //     'scene_2'         =>    2,
    //     'scene_3'         =>    3,
    //     'res_id'          =>    5851
    // ]
];