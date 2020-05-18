<?php

namespace app\common\validate;

use think\Validate;

class AdjustValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'start_time'   =>     'require',
        'start_min'    =>     'require',
        'end_time'     =>     'require',
        'end_min'      =>     'require',
        'light'        =>     'require|float',
        'id'           =>     'require|number',
        'adjustlist'   =>     'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];

    protected $scene = [
        'create'    =>    ['start_time','start_min','end_time','end_min','light'],
        'delete'    =>    ['id'],
        'batch'     =>    ['adjustlist'],
        'alter'     =>    ['id'],
    ];
}
