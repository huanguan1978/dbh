<?php
// declare (strict_types = 1);

// namespace app\behavior;
namespace Dbh\thinkphp\behavior;

class SessionChanged
{
    /**
     * 钩子行为处理（TP5行为处理）
     * 监听到SessionChange事件后用Plog记录当前的$_SESSION
     * Thinkphp5用的是PHP原生$_SESSION
     *
     * @return mixed
     */
    public function run($params=null)
    {
        //
        $data = [
            '$_SESSION'=>isset($_SESSION)?$_SESSION:[],
        ];        

        $mesg = sprintf('Behavior,SessionChanged.run,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        if(function_exists('app')){ // tp5.1及之后可以用app容器
            $plog = app('dbh')->wlog('db');
        }else{ // tp5.0及之前可以request()单例
            $plog = request()->dbh->wlog('db');
        }
        $plog->info($mesg, $data);
    }
}
