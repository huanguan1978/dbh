<?php

// namespace App\Subscribes;
namespace Dbh\Laravel\Subscribes;

class EnvChanged
{

    /**
     * Create the event subscriber.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }    

    public function onSessionChange($event)
    {
        // SessionChange 事件响应处理
        $data = [
            '$_SESSION'=>isset($_SESSION)?$_SESSION:[],
            'session'=>session()->all(),            
        ];        

        $mesg = sprintf('Event,onSessionChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);
    }

    public function onConfigChange($event)
    {
        // ConfigChange 事件响应处理，这里指的框架配置的变动
        $data = [
            'config'=>config()->all(), 
        ];        

        $mesg = sprintf('Event,onConfigChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);

    }


    public function onCookieChange($event)
    {
        // CookieChange 事件响应处理
        $data = [
            '$_COOKIE'=>isset($_COOKIE)?$_COOKIE:[],        
        ];        

        $mesg = sprintf('Event,onCookieChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);
    }

    public function onEnvChange($event)
    {
        // EnvChange 事件响应处理
        $data = [
            '$_ENV'=>isset($_ENV)?$_ENV:[],
        ];        

        $mesg = sprintf('Event,onEnvChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }


    public function onGlobalsChange($event)
    {
        // GlobalsChange 全局变量事件响应处理
        $data = [
            '$GLOBALS'=>$GLOBALS,
        ];        

        $mesg = sprintf('Event,onGlobalsChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }

    /**
     * 为订阅者注册监听器.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen('Dbh\Laravel\Events\SessionChange', 'Dbh\Laravel\Subscribes\EnvChanged@onSessionChange');
        $events->listen('Dbh\Laravel\Events\ConfigChange', 'Dbh\Laravel\Subscribes\EnvChanged@onConfigChange');        
        $events->listen('Dbh\Laravel\Events\CookieChange', 'Dbh\Laravel\Subscribes\EnvChanged@onCookieChange');        
        $events->listen('Dbh\Laravel\Events\EnvChange', 'Dbh\Laravel\Subscribes\EnvChanged@onEnvChange');
        $events->listen('Dbh\Laravel\Events\GlobalsChange', 'Dbh\Laravel\Subscribes\EnvChanged@onGlobalsChange');

    }    

    // cls_end
}


