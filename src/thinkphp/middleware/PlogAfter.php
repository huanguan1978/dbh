<?php
declare (strict_types = 1);

// namespace app\middleware;
namespace Dbh\thinkphp\middleware;

class PlogAfter
{
    /**
     * 处理请求,日志记录PATH_INFO请问访后响应内容
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {

		$response = $next($request);

        $data = [
            'Content'=>$response->getContent(),                    
            'headers_list'=>headers_list(),
            'apache_response_headers'=>apache_response_headers(),
            'http_response_code'=>http_response_code(),
            'session'=>session(''),            
        ];
        // var_dump($data['Content']);exit;
        $mesg = sprintf('Middleware,PlogAfter.handle,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);

        return $response;        
    }
}
