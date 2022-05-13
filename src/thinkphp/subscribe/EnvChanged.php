<?php
declare (strict_types = 1);

// namespace app\subscribe;
namespace Dbh\thinkphp\subscribe;

class EnvChanged
{
    public function onSessionChange()
    {
        // SessionChange 事件响应处理
        $data = [
            '$_SESSION'=>isset($_SESSION)?$_SESSION:[],
            'session'=>session(''), //tp6 session('') some Session::all()            
        ];        

        $mesg = sprintf('Event,onSessionChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);
    }

    public function onConfigChange()
    {
        // ConfigChange 事件响应处理，这里指的框架配置的变动
        $data = [
            'config'=>config(), 
        ];        

        $mesg = sprintf('Event,onConfigChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);

    }


    public function onCookieChange()
    {
        // CookieChange 事件响应处理
        $data = [
            '$_COOKIE'=>isset($_COOKIE)?$_COOKIE:[],        
        ];        

        $mesg = sprintf('Event,onCookieChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);
    }

    public function onEnvChange()
    {
        // EnvChange 事件响应处理
        $data = [
            '$_ENV'=>isset($_ENV)?$_ENV:[],
        ];        

        $mesg = sprintf('Event,onEnvChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }


    public function onGlobalsChange()
    {
        // GlobalsChange 全局变量事件响应处理
        $data = [
            '$GLOBALS'=>$GLOBALS,
        ];        

        $mesg = sprintf('Event,onGlobalsChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
        $plog = app('dbh')->wlog('db');
        $plog->info($mesg, $data);        
    }

/*
    public function subscribe(Event $event)  
    {
        $event->listen('SessionChange', [$this,'onSessionChange']);
        $event->listen('ConfigChange', [$this,'onConfigChange']);                           
        $event->listen('CookieChange', [$this,'onCookieChange']);
        $event->listen('EnvChange',[$this,'onEnvChange']);
        $event->listen('GlobalsChange',[$this,'onGlobalsChange']);
    }
*/
// cls_end
}
