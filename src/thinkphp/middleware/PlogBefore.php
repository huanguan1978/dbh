<?php
declare (strict_types = 1);

// namespace app\middleware;
namespace Dbh\thinkphp\middleware;

class PlogBefore
{
    /**
     * 处理请求,日志记录PATH_INFO请问访问时PHP原生Superglobal变量
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 
        $data = [
            '$_REQUEST'=>$_REQUEST,
            '$_SERVER'=>isset($_SERVER)?$_SERVER:[],
            '$_SESSION'=>isset($_SESSION)?$_SESSION:[],
            '$_ENV'=>isset($_ENV)?$_ENV:[],
            '$_FILES'=>$_FILES,
            'getallheaders'=>getallheaders(),
            'session'=>session(''),
        ];
        $mesg = sprintf('Middleware,PlogBefore.handle,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);

        return $next($request);
    }
}
