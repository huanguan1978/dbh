<?php

// namespace Config;
namespace Dbh\CodeIgniter\Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

 /*
Events::on('pre_system', static function () {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static function ($buffer) {
            return $buffer;
        });
    }


    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        Services::toolbar()->respond();
    }
});

*/

// 自定义事件, SessionChange，用Plog记录当前的$_SESSION
Events::on('SessionChange', static function () {
    $data = [
        '$_SESSION'=>isset($_SESSION)?$_SESSION:[],
    ];        

    $mesg = sprintf('Event.on,SessionChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
    $dbs = service('dbh');
    $plog = $dbs->wlog('db');
    $plog->info($mesg, $data);
});

// 自定义事件, GlobalsChange ，用Plog记录当前的$GLOBALS
Events::on('GlobalsChange', static function () {
    $data = [
        '$GLOBALS'=>$GLOBALS,
    ];        

    $mesg = sprintf('Event.on,GlobalsChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
    $plog = service('dbh')->wlog('db');
    $plog->info($mesg, $data);
});

// 自定义事件, EnvironChange ，用Plog记录当前的$_ENV
Events::on('EnvironChange', static function () {
    $data = [
        '$_ENV'=>isset($_ENV)?$_ENV:[],
    ];        

    $mesg = sprintf('Event.on,EnvironChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
    $plog = service('dbh')->wlog('db');
    $plog->info($mesg, $data);
});

// 自定义事件, CookieChange ，用Plog记录当前的$_ENV
Events::on('CookieChange', static function () {
    $data = [
        '$_COOKIE'=>isset($_COOKIE)?$_COOKIE:[],
    ];        

    $mesg = sprintf('Event.on,CookieChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
    $plog = service('dbh')->wlog('db');
    $plog->info($mesg, $data);
});

// 自定义事件, ConfigChange ，用Plog记录当前的$_ENV
Events::on('ConfigChange', static function () {

    $config = [];

    $paths = config('paths');
    $confpath = realpath($paths->appDirectory) . DIRECTORY_SEPARATOR .'Config' . DIRECTORY_SEPARATOR;

    $filenames = [];
    $d = dir($confpath);
    while (false !== ($entry = $d->read())) {
       $filename = $confpath . $entry;
       if(is_file($filename)){
            $filename = basename($entry, '.php');
            $filenames[] = $filename;
            $config[$filename] = config($filename);
       }
    }
    $d->close();


    $data = [
        'config'=>$config,
    ];        

    $mesg = sprintf('Event.on,ConfigChange,%s,%s', $_SERVER['REQUEST_METHOD'], $_SERVER['PATH_INFO']);
    $plog = service('dbh')->wlog('db');
    $plog->info($mesg, $data);
});