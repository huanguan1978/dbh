<?php

namespace Dbh\CodeIgniter\Config;

use CodeIgniter\Config\BaseService;

use Dbh\Dbh;

class Services extends BaseService {

    /**
     * 为CI4提供dbh实例服务
     * 服务自动发现，需在config/Autoload.php中$psr4数组中添加，如下
     * 'Dbh\\CodeIgniter' => ROOTPATH.'vendor\\orz\\dbh\\src\\CodeIgniter\\',
     * 调用方法如下：$dbh = service('dbh'); $query = $dbh->query('SELECT 1+1 AS two'); var_dump($query->fetchAll());
     * 
     * @return instance, 返回Dbh类初始化后的实例
     */
    static function dbh(bool $getShare=True){
        $inst = new Dbh();

        $logpath = config('Logger')->handlers['CodeIgniter\Log\Handlers\FileHandler']['path'];
        if(empty($logpath)){ $logpath = WRITEPATH.'logs';                       
        }        
        $inst->_fslog($logpath); // fslog object init 

        $link = $inst->_link_ci4db();
        // var_dump($link);exit;
        if($link){
            $inst->dbh('rw', $link['linker'], $link['driver']);
            $inst->_dblog($link['driver']); // dblog object init        
        }
        return $inst;        
    }
    
}