<?php

namespace app\common\validate;

use think\Validate;

class UserValidate extends BaseValidate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'username'     =>   'require|max:11',
        'password'     =>   'require|length:4,25',
        'repassword'   =>   'require|confirm:password',
        'scene_id'     =>   'require|number',
        'plain_id'     =>   'require|number',
        'mode_id'      =>   'require|number',
        'region_id'    =>   'require|number',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'repassword.confirm'=>'密码和确认密码不一致',
        'username.max'=>'用户名长度不能超过11',
        'password'=>'密码长度不在4-25之间'
    ];

    protected $scene=[
        'register'   =>    ['username','password','repassword'],
        'login'      =>    ['username','password'],
        'scene'      =>    ['scene_id'],
        'plain'      =>    ['plain_id'],
        'mode'       =>    ['mode_id'],
        'region'     =>    ['region_id']
    ];
}
