<?php
// declare (strict_types = 1);

// namespace app\behavior;
namespace Dbh\thinkphp\behavior;

class CookieChanged
{
    /**
     * 钩子行为处理（TP5行为处理）
     * 监听到 CookieChange 事件后用Plog记录当前的$_COOKIE
     *
     * @return mixed
     */
    public function run($params=null)
    {
        //
        $data = [
            '$_COOKIE'=>isset($_COOKIE)?$_COOKIE:[],
        ];        

        $mesg = sprintf('Behavior,CookieChanged.run,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }
}
