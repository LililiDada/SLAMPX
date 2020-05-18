<?php

namespace app\common\validate;

use think\Validate;

class ModeValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'pattern_id'      =>    'require',
        'modelist'        =>    'require',
        'id'              =>    'require|number',
        'page'            =>    'require|number',
        'num'             =>    'require|number',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];

    protected $scene = [
        'add'     =>     ['pattern_id','modelist'],
        'delete'  =>     ['id'],
        'list'    =>     ['pattern_id','page','num'],
    ];
}
