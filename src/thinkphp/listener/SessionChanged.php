<?php
declare (strict_types = 1);

// namespace app\listener;
namespace Dbh\thinkphp\listener;

class SessionChanged
{
    /**
     * 事件监听处理
     * 监听到SessionChange事件后用Plog记录当前的$_Session
     * Thinkphp6未用未原生$_Session可通过全局助手函数session()操作
     *
     * @return mixed
     */
    public function handle($event)
    {
        //
        $data = [
            '$_SESSION'=>isset($_SESSION)?$_SESSION:[],
            'session'=>session(''), //tp6 session('') some Session::all()            
        ];        

        $mesg = sprintf('Event,SessionChanged.handle,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }
}
