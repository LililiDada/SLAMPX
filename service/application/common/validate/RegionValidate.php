<?php

namespace app\common\validate;

use think\Validate;

class RegionValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'regionname' =>    'require',
        'id'          =>   'require|integer|>:0',
        'page'        =>   'integer|>:0',
        'num'         =>   'integer|>:0',
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
        'add'      =>   'regionname',
        'delete'   =>   'id',
        'list'    =>    ['page','num'],
    ];
}
