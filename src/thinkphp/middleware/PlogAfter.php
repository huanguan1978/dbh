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
     * @param int $longtime=3 slow-queries-time, default 3 second
     * @return Response
     */
    public function handle($request, \Closure $next, int $logtime=3)
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

        $plog = app('dbh')->wlog('db');
        if(isset($_SERVER['REQUEST_TIME']) && ($longtime >0) ){ // log slow queries, default 3MS
            $interval = time() - $_SERVER['REQUEST_TIME'];
            if($interval>$longtime){
                $mesg = sprintf('Middleware,PlogAfter.handle-slow_%s,%s,%s', $interval, $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
                $plog->warning($mesg, $data);
            }
        }

        $mesg = sprintf('Middleware,PlogAfter.handle,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog->info($mesg, $data);

        return $response;
    }
}
