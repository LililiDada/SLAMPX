<?php

namespace app\http\middleware;


class ApiUserAuth
{
    public function handle($request, \Closure $next)
    {

        $param = $request->header();
    
        if (!array_key_exists('token',$param)) TApiException('非法token，禁止操作',20003,200); 
    
        $token = $param['token'];
        $user = \Cache::get($token);
        if(!$user) TApiException('非法token，请重新登录',20003,200);
        $request->userToken = $token;
        $request->userId = $user['id'];
        $request->userTokenUserInfo = $user; 
        
        return $next($request);
    }
    
}
