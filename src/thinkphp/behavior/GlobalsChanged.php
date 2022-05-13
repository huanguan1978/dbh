<?php
// declare (strict_types = 1);

// namespace app\behavior;
namespace Dbh\thinkphp\behavior;

class GlobalsChanged
{
    /**
     * 钩子行为处理（TP5行为处理）
     * 监听到 GlobalsChange 事件后用Plog记录当前的 $GLOBALS
     *
     * @return mixed
     */
    public function run($params=null)
    {
        //
        $data = [
            '$GLOBALS'=>$GLOBALS,
        ];        

        $mesg = sprintf('Behavior,GlobalsChanged.run,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }
}
