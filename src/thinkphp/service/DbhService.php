<?php
declare (strict_types = 1);

// namespace app\service;
namespace Dbh\thinkphp\service;

use Dbh\Dbh;
class DbhService extends \think\Service
{
    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {
    	// 邦定到闭包产生的实例
        $this->app->bind('dbh', function(){
            return new Dbh();            
        });
    }

    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot()
    {
        // 数据对象赋值
        $inst = app('dbh');

        $logpath = config('log.channels.file.path');
        if(empty($logpath)){
            // TP5.1之前有常量LOG_PATH之后无常量可用app()->getRootPath()拼接
            $logpath = defined('LOG_PATH')?LOG_PATH:(app()->getRuntimePath().'logs');
        }
        $inst->_fslog($logpath); // fslog object init 

        $link = $inst->_link_tp6db();
        if($link){
            $inst->dbh('rw', $link['linker'], $link['driver'], $link['optional']);
            $inst->_dblog($link['driver']); // dblog object init                    
        }        
    }
}
