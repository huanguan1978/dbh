<?php
// declare (strict_types = 1);

// namespace app\behavior;
namespace Dbh\thinkphp\behavior;

class ConfigChanged
{
    /**
     * 钩子行为处理（TP5行为处理）
     * 监听到 ConfigChange 事件后用Plog记录当前的config()
     *
     * @return mixed
     */
    public function run($params=null)
    {
        //
        $data = [
            'config'=>config(), 
        ];          

        $mesg = sprintf('Behavior,ConfigChanged.run,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }
}
