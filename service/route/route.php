<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::group('api/',function(){
    // 登录
    Route::post("user/login","api/User/login");
    // 注册
    Route::post("user/register","api/User/register");
    // 获取模式列表
    Route::get("pattern","api/Pattern/index");
    Route::get('device/fd','api/Device/fd');
    //对接oneNet
    Route::rule('message/abutment','api/Message/abutment');
});

Route::group('api/',function(){
    // 用户绑定场景模式
    Route::post('user/bindscene','api/User/bindScene');
    // 用户控制开关模式
    Route::post('user/bindmode','api/User/bindMode');
    // 用户绑定默认普通模式
    Route::post('user/bindplain','api/User/bindPlain');
    // 获取设备
    Route::get('device','api/Device/index');
    // 获取部分列表
    Route::get('device/getdevice/:page/:num','api/Device/getDevice');
    // 获取区域下设备ID
    Route::get('device/getdeviceid','api/Device/getDeviceId');
    // 获取当前设备
    Route::get('device/devicedetail/:id','api/Device/deviceDetail');
    // 添加设备
    Route::post('device/adddevice','api/Device/addDevice');
    // 删除设备
    Route::post('device/deletedevice','api/Device/deleteDevice');
    // 添加区域
    Route::post('region/addregion','api/Region/addRegion');
    // 获取区域
    Route::get('region/getregion/[:page]/[:num]','api/Region/getRegion');
    // 获取全部区域
    Route::get('region/allregion','api/Region/allRegion');
    // 删除区域
    Route::post('region/deleteregion','api/Region/deleteRegion');
    // 设备绑定区域
    Route::post('device/bindregion','api/Device/bindRegion');
    // 设备绑定场景模式
    Route::post('device/bindscene','api/Device/bindScene');
    // 设备绑定默认普通模式
    Route::post('device/bindplain','api/Device/bindPlain');
    // 获取某一区域的设备列表
    Route::get('device/regiondevice/:region_id/:page/[:num]','api/Device/regionDevice');
    // 添加模式下的亮度
    Route::post('adjust/create','api/Adjust/create');
    // 删除亮度时间段
    Route::post('adjust/delete','api/Adjust/delete');
    // 批量删除亮度时间段
    Route::post('adjust/batchdelete','api/Adjust/batchDelete');
    // 修改亮度时间段
    Route::post('adjust/alter','api/Adjust/alter');
    // 添加模式
    Route::post('mode/add','api/Mode/add');
    // 删除模式
    Route::post('mode/delete','api/Mode/delete');
    // 获取指定模式下的数据
    Route::get('mode/:pattern_id/getlist/:page/:num','api/Mode/getList');
    // 添加故障
    Route::post('repair/addrepair','api/Repair/addRepair');
    // 获取故障列表
    Route::get('repair/:status/getrepair/:page/[:num]','api/Repair/getRepair');
    // 获取设备七个五天一次的故障数量
    Route::get('repair/statisnum','api/Repair/statisNum');
    // 数据统计页面获取亮灯率，故障数量，开关时间
    Route::get('user/gethistory','api/user/getHistory');
    // 数据统计页面获取区域下亮灯率，故障数量，开关时间
    Route::get('user/regionhistory/:region_id','api/user/regionHistory');
    // 历史故障，紧急维修、日常维修的数量
    Route::get('repair/repairnum','api/Repair/repairNum');
    // 获取具体相关设备的历史数据
    Route::get('message/datastreams','api/Message/datastreams');
    // 按月统计用电量
    Route::get('message/monthstatis','api/Message/monthStatis');
	 // 发送命令
    Route::post('device/sendcommand','api/Device/sendCommand');
    
})->middleware('Auth');

// socket和对接oneNet部分
Route::group('api/',function(){
    //  绑定上线
    Route::post('message/bind','api/Message/bind');
    
})->middleware(['ApiUserAuth']);