<?php

namespace app\common\validate;
use think\Validate;
use app\lib\exception\BaseException;
class BaseValidate extends Validate
{
    public function goCheck($scene = false)
    {
        // 获取请求传递过来的所有参数
       $params = request()->param();
        // 开始验证
        $check = $scene?
                $this->scene($scene)->check($params):
                $this->check($params);
        if(!$check){
            throw new BaseException(['msg'=>$this->getError(),'errorCode'=>10000,'code'=>400]);
        }

        return true;
    }
}
