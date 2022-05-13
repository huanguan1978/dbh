<?php

// namespace App\Listeners;
namespace Dbh\Laravel\Listeners;

use App\Events\SessionChange;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SessionChanged
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 监听到SessionChange事件后用Plog记录当前的$_Session
     * Laravel未用未原生$_Session可通过全局助手函数session()操作
     * 
     * @param  object  $event
     * @return void
     */
    public function handle(\Dbh\Laravel\Events\SessionChange $event)
    {
        //
        $data = [
            '$_SESSION'=>isset($_SESSION)?$_SESSION:[],
            'session'=>session()->all(),
        ];        

        $mesg = sprintf('Event,SessionChanged.handle,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);

    }

}
