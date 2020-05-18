<?php

namespace app\common\validate;

use think\Validate;

class RepairValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'fault'        =>     'require',
        'device_id'    =>     'require|number',
        'status'       =>     'require|number',
        'page'         =>     'require|number'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];

    protected $scene = [
        'add'   =>   ['fault','device_id','status'],
        'get'   =>   ['status','page']
    ];
}
