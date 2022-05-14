<?php
/**
 * TP5可用的Provider
 */

namespace Dbh\thinkphp\provider;

use Dbh\Dbh;

class DbhProvider {

    /**
     * TP51容器邦定类自定义实例
     * @return object 返回已初始化的Dbh类实例
     */
    static function __make(){
        // 数据对象赋值
        $inst = new Dbh();

        $logpath = config('log.channels.file.path');
        if(empty($logpath)){
            // TP5.1之前有常量LOG_PATH之后无常量可用app()->getRootPath()拼接
            $logpath = defined('LOG_PATH')?LOG_PATH:(app()->getRuntimePath().'log');
        }
        $inst->_fslog($logpath); // fslog object init 

        $link = $inst->_link_tp5db();
        if($link){
            $inst->dbh('rw', $link['linker'], $link['driver'], $link['optional']);
            $inst->_dblog($link['driver']); // dblog object init                    
        }

        return $inst;
    }

    /**
     * TP50 Request中注入自定义实例
     * @return object 返回已初始化的Dbh类实例
     */
    static function invoke(\think\Request $request){
        // 数据对象赋值
        $inst = new Dbh();

        $logpath = config('log.channels.file.path');
        if(empty($logpath)){
            // TP5.1之前有常量LOG_PATH之后无常量可用app()->getRootPath()拼接
            $logpath = defined('LOG_PATH')?LOG_PATH:(app()->getRuntimePath().'log');
        }
        $inst->_fslog($logpath); // fslog object init 

        $link = $inst->_link_tp5db();
        if($link){
            $inst->dbh('rw', $link['linker'], $link['driver'], $link['optional']);
            $inst->_dblog($link['driver']); // dblog object init                    
        }

        // 绑定实例到当前请求对象(TP50属性注入)
        // 助手函数调用方法 request()->dbh;
        $request->instance()->bind('dbh', $inst);

        return $inst;
    }

    // cls.end
}