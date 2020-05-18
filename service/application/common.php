<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


// 异常类输出函数
function TApiException($msg='异常',$errorCode =999,$code = 400)
{
    throw new app\lib\exception\BaseException(['code'=>$code,'msg'=>$msg,'errorCode'=>$errorCode]);
}

