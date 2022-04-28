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

    function __construct (){
    }

    function __destruct(){
    }

    /**
      传递且包装PDO连接表为Plogger对象
      @param string $tablename 日志存放在那个数据表
      @param PDO $pod PDO连接对象
      @return object 已实例化过的logger对象
    */
    protected function _dblog(string $tablename, PDO $pdo){
        $log = Plogger::getInstance();
        $log->setOptions(['logFormat' => 'Y-m-d H:i:s']);
        $log->dblink($pdo);
        $log->dbtable($table);
        $this->_log['db'] = $log;
        return $this->_log['db'];
    }

    /**
      传递且包装filename文件名为logger对象
      @param string $path 日志存放在那个目录
      @param string $file 日志存放在那个文件
      @return object 已实例化过的logger对象
    */
    protected function _fslog(string $path=null, string $file=null){
        $log = Tlogger::getInstance();
        $log->setOptions(['logFormat' => 'Y-m-d H:i:s']);
        $log->pathfile($path, $file);
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
      传递且包装PDO连接为Medoo对象
      @param string $name 那种连接类型 rw|ro|wo即读写|仅读|仅写
      @param string $pdo 替换$name指定类型的PDO对象实例
      @return object 已实例化过的Medoo对象
    */
    function dbh(string $name='rw', $pdo=null, string $dbtype=null):Medoo {
        if($pdo){
            $param = ['pdo'=>$pdo];
            if(empty($dbtype)){
                $dbtype = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            }
            if($dbtype){
                $param['type'] = $dbtype;
            }
            $mdb = new Medoo($param);
            $this->_dbh[$name] = $mdb;
            if($name=='rw'){
                if(empty($this->_dbh['wo'])){
                    $this->_dbh['dbwo'] =  $mdb;
                }
                if(empty($this->_dbh['dbro'])){
                    $this->_dbh['dbro'] =  $mdb;
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
      获取TP5的PDO连接对象，用think\Db
      @return object 已实例化过的PDO对象
    */
    function _link_tp5db(){
        $link = null;
        $cls = get_declared_classes();
        if(in_array('think\Db', $cls)){
            $inst =  Db::connect();
            $rst = $inst->query('select 1;');
            $link = $inst->getConnection();
            $link = $link->getPdo();
        }
        return $link;
    }

    /**
      获取TP5的PDO连接对象，用think\Config
      @return object 已实例化过的PDO对象
    */
    function _link_tp5cnf(){
        $link = null;
        $cls = get_declared_classes();
        if(in_array('think\Db', $cls)){
            $cnf = Db::getConfig();
            $dsn = sprintf("%s:dbname=%s;host=%s", $cnf['type'], $cnf['database'], $cnf['hostname']);
            $link = new PDO($dsn, $cnf['username'], $cnf['password']);
        }
        return $link;
    }

    /**
      获取Lv6的PDO连接对象，用门面Db
      @return object 已实例化过的PDO对象
    */
    function _link_lv6db(string $name=null){
        $link = null;
        $cls = get_declared_classes();
        if(in_array('Db', $cls)){
            $link = DB::connection($name)->getPdo();
        }
        return $link;
    }


    //cls.end
}
