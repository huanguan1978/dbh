<?php
/**
  Dbh即DatbaseHandle就是数据库连接句柄，
  创建与framework分离的module,可解救你与多个框架间开发同一项目时的数据模型多重编码问题
*/

namespace Dbh;

use GUMP;
use Medoo\Medoo;

use Plog\Plogger;
use Plog\Tlogger;

class Dbh {

    /**
      $_dbh - 数据库资源阵列
      @var array
    */
    
    private $_dbh = [
        'rw'=>null, // 读写
        'wo'=>null, // 仅写
        'ro'=>null, // 仅读
    ];

    /**
      $_log - 日志资源阵列
      @var array
    */
    private $_log = [
        'db'=>null, // 数据库
        'fs'=>null, // 文件系统
    ];

    function __construct ($dbh=null){
        if($dbh){
            $this->dbh('rw', $dbh);
        }
    }

    function __destruct(){
    }

    /**
      传递且包装PDO连接表为Plogger对象
      @param string $tablename 日志存放在那个数据表
      @param PDO $pod PDO连接对象
      @return object 已实例化过的logger对象
    */
    function _dblog(string $drivername, string $tablename=null, \PDO $pdo=null){
        if(empty($tablename)){ $tablename = '_plog';
        }
        if(empty($pdo)){ $pdo = $this->dbh('wo')->pdo;
        }

        $log = Plogger::getInstance();
        $log->setOptions(['logFormat' => 'Y-m-d H:i:s']);
        $log->dbtype($drivername);        
        $log->dblink($pdo);
        $log->dbtable($tablename,true);
        $this->_log['db'] = $log;
        return $this->_log['db'];
    }

    /**
      传递且包装filename文件名为logger对象
      @param string $path 日志存放在那个目录
      @param string $file 日志存放在那个文件
      @return object 已实例化过的logger对象
    */
    function _fslog(string $path=null, string $file=null){
        if($path && (substr($path, -1)==DIRECTORY_SEPARATOR)){
                $path = substr($path, 0, -1);
        }
        $log = Tlogger::getInstance();
        $log->setOptions(['logFormat' => 'Y-m-d H:i:s']);
        $log->logpath($path);
        if($file){ $log->log_file = $file;
        }
        $this->_log['fs'] = $log;
        return $this->_log['fs'];
    }

    /**
      传递封装好的Plogger或Tlogger对象到_log阵列中
      @param string $type 那种日志类型 db|fs
      @param string $inst 替换$type指定类型的Logger对象实例
      @return object 已实例化过的logger对象
    */
    function wlog($type='fs', $inst=null){
        if($inst){
            $this->_log[$type] = $inst;
        }

        return $this->_log[$type];
    }

    /**
     * 传递且包装PDO连接为Medoo对象
     * @param string $name 那种连接类型 rw|ro|wo即读写|仅读|仅写
     * @param string $pdo 替换$name指定类型的PDO对象实例
     * @param array $optioanl 初始化Medoo对象时的选参
    */
    function dbh(string $name='rw', $pdo=null, string $dbtype=null, array $optional=null):Medoo {
        if($pdo){
            $param = ['pdo'=>$pdo];
            if(empty($dbtype)){
                $dbtype = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            }
            if($dbtype){
                $param['type'] = $dbtype;
            }
            if($optional){
                $param = array_merge($optional, $param);
            }

            $mdb = new Medoo($param);
            $this->_dbh[$name] = $mdb;
            if($name=='rw'){
                if(empty($this->_dbh['wo'])){
                    $this->_dbh['wo'] =  $mdb;
                }
                if(empty($this->_dbh['ro'])){
                    $this->_dbh['ro'] =  $mdb;
                }
            }
        }
        return $this->_dbh[$name];
    }

    /**
      数据校验器
      @param string $data 要校验的数据
      @param string $rule 校验规则集
      @param string $fliter 数据过滤集
      @param string $emsg 自定校验错误显示信息
      @return array 校验结果,如[0, $data, $emsg], 索引1值0有错1无值，索引2处理过的数据，索引3出错清单
    */
    function validate(array $data, array $rule, array $fliter=[], array $emsg=[] ):array {
        $gump = new GUMP();
        $gump->validation_rules($rule);
        $gump->set_fields_error_messages($emsg);
        $gump->filter_rules($fliter);
        $data = $gump->run($data);

        $result = [0, $data, $emsg];
        if($gump->errors()){
            $result[2] = $gump->get_errors_array();
        }else{ // successs
            $result[0]=1;
            $result[2] = [];
        }

        return $result;
    }

    /**
      获取TP5的PDO连接对象，用门面\think\Db的connect获得实例
      @return array 数组结构返回，键driver为数据驱动器名称，键linker为数据连接器实例
    */
    function _link_tp5db():array {
        $link = [];

        $inst = \think\Db::connect();
        $rst = $inst->query('select 1;');
        $conn = $inst->getConnection();
        $conf = $conn->getConfig();
        $link = [
            'linker'=>$conn->getPdo(),
            'driver'=>$conf['type'],
            'optional'=>[
                'prefix'=>$conf['prefix'],
                'logging'=>$conf['debug'],
            ],
        ];
        $optional = ['charset', 'collation', 'port', ];
        foreach($optional as $item){
            if(isset($conf[$item]) && !empty($conf[$item]) ){
                $link['optional'][$item] = $conf[$item];
            }    
        }

        return $link;
    }

   /**
      获取TP6的PDO连接对象，用助手函数app(db)的connect获得实例
      @return array 数组结构返回，键driver为数据驱动器名称，键linker为数据连接器实例
    */
    function _link_tp6db():array {
        $link = [];

        $inst = app('db');
        $conn = $inst->connect();
        $rst = $inst->query('select 1;');

        $conf = $conn->getConfig();
        $link = [
            'linker'=>$conn->getPdo(),
            'driver'=>$conf['type'],
            'optional'=>[
                'prefix'=>$conf['prefix'],
                'logging'=>$conf['debug'],
            ],
        ];
        $optional = ['charset', 'collation', 'port', ];
        foreach($optional as $item){
            if(isset($conf[$item]) && !empty($conf[$item]) ){                
                $link['optional'][$item] = $conf[$item];
            }    
        }

        return $link;
    }


    /**
      获取Lv6的PDO连接对象，用助手函数app获取db.connection实例
      @return array 数组结构返回，键driver为数据驱动器名称，键linker为数据连接器实例
    */
    static function _link_lv6db(string $name=null):array {
        $link = [];
        $inst = app('db.connection');

        $conf = $inst->getConfig();
        if($inst){
            $link = [
                'linker'=>$inst->getPdo(),
                'driver'=>$inst->getDriverName(),
                'optional'=>[
                    'prefix'=>$conf['prefix'],
                ],                
             ];
        }
        $optional = ['charset', 'collation', 'port', ];
        foreach($optional as $item){
            if(isset($conf[$item]) && !empty($conf[$item]) ){
                $link['optional'][$item] = $conf[$item];
            }    
        }

       return $link;
    }


   /**
      获取CI4的PDO连接对象，用db_connet()实例的属性拼接DSN后初始化PDO实例
      @return array 数组结构返回，键driver为数据驱动器名称，键linker为数据连接器实例
    */
    function _link_ci4db():array {
        $link = [];

        $inst = db_connect();
        $_driver = $driver = $inst->DBDriver;
        if($driver=='MySQLi'){            $_driver = 'mysql';        }
        if($driver=='Postgre'){            $_driver = 'pgsql';        }
        if($driver=='SQLite3'){            $_driver = 'sqlite';        }
        if($driver=='SQLSRV'){            $_driver = 'sqlsrv';        }

        $dsn = sprintf('%s:host=%s;port=%s;dbname=%s', 
            $_driver, $inst->hostname, $inst->port, $inst->database);
        if($driver=='SQLSRV'){
            $dsn = sprintf('%s:Server=%,%s;Datebase=%s', 
            $_driver, $inst->hostname, $inst->port, $inst->database);
        }


        $linker = null;
        try {
            $linker = new \PDO($dsn, $inst->username, $inst->password);
        } catch (\PDOException $e) {
            die($e->getMessage() );
        }
        // var_dump(config('Database'));exit;

        $link = [
            'linker'=>$linker,
            'driver'=>$_driver,
            'optional'=>[
                'prefix'=>$inst->DBPrefix,
                'logging'=>$inst->DBDebug,
            ],               
        ];

        $optional = ['charset'=>'charset', 'collation'=>'DBCollat', 'port'=>'port', ];
        foreach($optional as $k=>$v){
            if(isset($inst->$v) && !empty($inst->$v) ){
                $link['optional'][$k] = $inst->$v;
            }    
        } 

        return $link;
    }

   /**
      获取CI3的PDO连接对象，用get_instance()实例的db下的conn_id属性获得实例
      需要CI3的config/database.php中dbdriver='pdo'
      @return array 数组结构返回，键driver为数据驱动器名称，键linker为数据连接器实例
    */
    function _link_ci3db():array {
        $link = [];

        $inst = & get_instance();
        $db = $inst->db;
        $driver = $db->dbdriver;
        if('pdo' == $driver){
            $driver = $db->subdriver;
        }

        $link = [
            'linker'=>$inst->db->conn_id,
            'driver'=>$driver,
            'optional'=>[
                'prefix'=>$inst->dbprefix,
                'logging'=>$inst->db_debug,
            ],                 
        ];

        $optional = ['charset'=>'char_set', 'collation'=>'dbcollat', 'port'=>'port', ];
        foreach($optional as $k=>$v){
            if(isset($inst->$v) && !empty($inst->$v) ){
                $link['optional'][$k] = $inst->$v;
            }    
        }        

        return $link;
    }



    function _get_declared_classes() {
        return get_declared_classes();
    }

    //cls.end
}
