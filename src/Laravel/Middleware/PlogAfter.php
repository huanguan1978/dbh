<?php

// namespace App\Http\Middleware;
namespace Dbh\Laravel\Middleware;

use Closure;

class PlogAfter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response =  $next($request);

        $data = [
            'Content'=>$response->getContent(),                    
            'headers_list'=>headers_list(),
            'apache_response_headers'=>apache_response_headers(),
            'http_response_code'=>http_response_code(),
            'session'=>session()->all(),
        ];
        // var_dump($data['SEND']);exit;
        $mesg = sprintf('Middleware,PlogAfter.handle,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);
        
        return $response;
    }
}
