<?php
// declare (strict_types = 1);

// namespace app\behavior;
namespace Dbh\thinkphp\behavior;

class EnvironChanged
{
    /**
     * 钩子行为处理（TP5行为处理）
     * 监听到EnvironChange事件后用Plog记录当前的$_ENV
     * Thinkphp5用的是PHP原生$_ENV
     *
     * @return mixed
     */
    public function run($params=null)
    {
        //
        $data = [
            '$_ENV'=>isset($_ENV)?$_ENV:[],
        ];        

        $mesg = sprintf('Behavior,EnvironChanged.run,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }

}
