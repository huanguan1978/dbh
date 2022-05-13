<?php

// namespace App\Http\Middleware;
namespace Dbh\Laravel\Middleware;

use Closure;

class PlogBefore
{
    /**
     * Handle an incoming request.
     * 处理请求,日志记录PATH_INFO请问访问时PHP原生Superglobal变量
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 
        $data = [
            '$_REQUEST'=>$_REQUEST,
            '$_SERVER'=>isset($_SERVER)?$_SERVER:[],
            '$_SESSION'=>isset($_SESSION)?$_SESSION:[],
            '$_ENV'=>isset($_ENV)?$_ENV:[],
            '$_FILES'=>$_FILES,
            'getallheaders'=>getallheaders(),
            'session'=>session()->all(),
        ];
        $mesg = sprintf('Middleware,PlogBefore.handle,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);

        return $next($request);
    }
}
