<?php

namespace app\common\controller;

use think\Controller;
use think\Request;

class BaseController extends Controller
{
    // api统一返回格式
    static public function showResCode($msg='未知',$data=[],$code=200)
    {
        $res=[
            'msg'=>$msg,
            'data'=>$data
        ];
        return json($res,$code);
    }

    static public function showResCodeWithOutData($msg='未知',$code =200){
        return self::showResCode($msg,[],$code);
    }
}
