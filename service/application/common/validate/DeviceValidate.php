<?php

namespace app\common\validate;

use think\Validate;

class DeviceValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'longitude'   =>   'require|between:-180,180',
        'latitude'    =>   'require|between:-180,180',
        "lamp_id"     =>   'require|number',
        'region_id'   =>   'number',
        'scene_id'    =>   'require|number',
        'plain_id'    =>   'require|number',
        'id'          =>   'require|integer|>:0',
        'page'        =>   'integer|>:0',
        'num'         =>   'integer|>:0',
        'imei'        =>   'require',
        'regionid'    =>   'require',
		'type'        =>   'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [

    ];

    protected $scene = [
        'add'      =>  ['longitude','latitude','lamp_id','imei'],
        'delete'   =>  'id',
        'region'   =>  ['region_id','id'],
        'scene'    =>  ['scene_id','id'],
        'plain'    =>  ['plain_id','id'],
        'list'     =>  ['region_id','page'],
        'index'    =>  ['page','num'],
        'deviceid' =>  ['regionid','type'],
        'command'  =>  ['scene_id','regionlist','mode']
    ];
}
