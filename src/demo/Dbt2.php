<?php

require '../../vendor/autoload.php';

use Dbh\Dbh;

/**
 *数据增删改查演示
 *
 */
class Dbt2 {

    /**
     * @property \Dbh\Dbh $dbi Dbh实例
     */
    protected $dbi;

    /**
     * @property array $matedata 元数据表属性定义
     */
    protected $md = [
        'table'=> '',
        'primaryKey'=> '',
        'returnType'=> 'array',
        'useSoftDeletes'=> true,
        'allowedFields'=> [],
        'useTimestamps'=> false,
        'createdField'=> '',
        'updatedField'=> '',
        'deletedField'=> '',
        'validationRules'=> [],
        'validationMessages'=> [],
        'skipValidation'=> false
    ];

    /**
     * @property array $dats 测试数据
     */
    protected $dats = [
        ['id'=>null, 'time'=>null, 'severity'=>'NOTICE', 'message'=>'notice-a', ],
        ['id'=>null, 'time'=>null, 'severity'=>'NOTICE', 'message'=>'notice-b', ],
        ['id'=>null, 'time'=>null, 'severity'=>'NOTICE', 'message'=>'notice-c', ],
        ['id'=>null, 'time'=>null, 'severity'=>'NOTICE', 'message'=>'notice-d', ],
        ['id'=>null, 'time'=>null, 'severity'=>'NOTICE', 'message'=>'notice-e', ],
    ];

    /**
     * 设置获取dbi实例
     * @param \Dbh\Dbh $dbi Dbh实例
     * @return \Dbh\Dbh Dbh实例
     */
    function dbh(object $dbh=null){
        if($dbh){
            $this->dbh = $dbh;
        }
        return $this->dbh;
    }

    /**
     * 设置获取元数据表属性定义
     * @param array $matedata 表属性定义
     * @return array  表属性定义
     */
    function matedata(array $matedata=null){
        if($matedata){
            foreach($this->md as $k=>$v){
                if(array_key_exists($k, $matedata) && (gettype($this->md[$k])==gettype($v)) ){
                    $this->md[$k] = $matedata[$k];
                }
            }
        }
        return $this->md;
    }

    /**
     * 显示数据表格
     * @param array  $names 逗号分隔的数据字段表头
     * @param array   $rows 数据记录
     * @return string 数据表格HTML的TABLE部份
     */
    function displayTable(array $names, array $rows):string {
        $html = '';
        $html .= '<table>';
        $html .=  '<tr>';
        foreach($names as $name){
            $html .=  "<th>$name</th>";
        }
        $html .=  '</tr>';
        foreach($rows as $row){
            $html .=  '<tr>';
            foreach($names as $name){
                $html .=  "<td>$row[$name]</td>";
            }
            $html .=  '</tr>';
        }
        $html .=  '<table>';
        return $html;
    }

    /**
     * 显示已执行的SQL
     * @param string  $name 你的函数名
     * @param array   $sqls debugLog返回的Sql
     * @return string 数据表格HTML的DLDTDD部份
     */
    function displaySql(string $name, array $sqls):string {
        $html = '';
        $html .=  '<dl>';
        $html .=  "<dt>$name</dt>";
        foreach($sqls as $sql){
            $html .=  "<dd>$sql</dd>";
        }
        $html .=  '</dl>';
        return $html;
    }

    /**
     * 获取表中全部数据
     *
     * @return array 表中所有记录
     */
    function get_1_test(){
        $ro = $this->dbh->dbh('ro');
        $rows = $ro->get($this->md['table']);
        return $rows;
    }

    /**
     * 获取表中指定列的数据
     *
     * @return array 指定列的所有记录
     */
    function get_2_test(){
        $columns = '*';
        $columns = 'id, time, severity, message';

        $ro = $this->dbh->dbh('ro');
        $rows = $ro->get($md['table'], $columns);
        return $rows;
    }


    /**
     * 获取表中指定列且满足条件的所有数据
     *
     * @return array 满足条件的所有记录
     */
    function get_3_test($display=0){
        $columns = '*';
        $columns = 'id,time,severity,message';
        $columns = explode(',', $columns);
        $where = ['severity'=>'NOTICE',];

        $ro = $this->dbh->dbh('ro');
        $dlog = [];

        $rows = $ro->select($this->md['table'], $columns, $where);
        $dlog[] = $ro->last();
        if ($dlog && $display) {
            echo $this->displaySql(__METHOD__, $dlog);
        }
        if($rows && $display){
            echo $this->displayTable($columns, $rows);
        }

        return $rows;
    }

        /**
     * 获取表中指定列且满足条件的单条数据
     *
     * @return array 满足条件的单条记录
     */
    function get_4_test($display=0){
        $columns = '*';
        $columns = 'id,time,severity,message';
        $columns = explode(',', $columns);
        $where = ['severity'=>'NOTICE',];

        $ro = $this->dbh->dbh('ro');
        $dlog = [];

        //        $row = $ro->get($this->md['table'], $columns, $where);
        $row = $ro->get($this->md['table'], '*', $where);
        $rows[] = $row;
        $dlog[] = $ro->last();
        if ($dlog && $display) {
            echo $this->displaySql(__METHOD__, $dlog);
        }
        if($rows && $display){
            echo $this->displayTable($columns, $rows);
        }

        return $rows;
    }

    /**
     *创建数据，逐条插入测试数据
     *
     * @return int 插入条数
     */
    function ins_dats_test($display=0){
        $rw = $this->dbh->dbh('rw');
        $dlog = [];

        $affected_rows = 0;
        foreach($this->dats as $i=>$dat){
            $dat = ['time'=>$rw->raw('NOW()'), 'severity'=>$dat['severity'], 'message'=>$dat['message'], ];
            $pdostmt = $rw->insert($this->md['table'], $dat);
            $dlog[] = $rw->last();
            if($rw->error){
                $dlog[] =$rw->error;
            }else{
                $affected_rows += 1;
            }
            $this->dats[$i]['id']=$rw->id();
        }

        if ($dlog && $display) {
            echo $this->displaySql(__METHOD__, $dlog);
        }

        $rows = $this->dats;
        return $rows;
    }

    /**
     *删除数据，删除提定ID的测试数据
     *
     * @return int 删除条数
     */
    function del_dats_test($display=0){
        $rw = $this->dbh->dbh('rw');
        $dlog = [];

        $ids=[];
        foreach($this->dats as $i=>$dat){
            if($dat['id']){
                $ids[] = $dat['id'];
            }
        }
        $affected_rows = 0;
        if($ids){
            $where = [$this->md['primaryKey']=>$ids, ]; // WHERE id IN (1,2,3);
            $pdostmt = $rw->delete($this->md['table'], $where);
            $dlog[] = $rw->last();
            if($rw->error){
                $dlog[] =$rw->error;
            }else{
                $affected_rows += 1;
            }
            // $affected_rows = $pdostmt->rowCount();
        }

        if ($dlog && $display) {
            echo $this->displaySql(__METHOD__, $dlog);
        }

        return $affected_rows;
    }

    /**
     *更新数据，更新提定ID的测试数据
     *
     * @return int 更新条数
     */
    function upd_dats_test($display=0){
        $rw = $this->dbh->dbh('rw');
        $dlog = [];

        $affected_rows = 0;
        foreach($this->dats as $i=>$dat){
            $this->dats[$i]['time'] = $rw->raw('NOW()');
            $this->dats[$i]['message'].='-1';

            $updat = ['time'=>$this->dats[$i]['time'], 'message'=>$this->dats[$i]['message'], ];
            $where = [$this->md['primaryKey']=>$this->dats[$i]['id'], ];
            $pdostmt = $rw->update($this->md['table'], $updat, $where);
            $dlog[] = $rw->last();
            if($rw->error){
                $dlog[] =$rw->error;
            }else{
                $affected_rows += 1;
            }
            // $affected_rows += $pdostmt->rowCount();
        }

        if ($dlog && $display) {
            echo $this->displaySql(__METHOD__, $dlog);
        }

        return $affected_rows;
    }

    // cls.end
}

/**
 * 子程序
 * @return void 返回无类型
 */
function main(){
    ini_set('display_errors',1);
    error_reporting(E_ALL);

    $drive_name = 'mysql';
    $dbname = 'test';
    $host = '127.0.0.1';
    $username = 'root';
    $password = 'youpassword';
    $dsn = "$drive_name:host=$host;dbname=$dbname";
    $table = '_plog';
    $primaryKey = 'id';

    // 1.获得PDO实例
    $pdo = new \PDO($dsn, $username, $password);
    // 2.获得Dbh实例
    $inst = new Dbh();
    // 3.获得Medoo实例
    $dbh = $inst->dbh('rw', $pdo, $drive_name);
    // 4.获得Plog实例
    $log = $inst->_dblog($drive_name, $table, $pdo);

    // 5.获取DEMO实例
    $demo = new Dbt2();
    // var_dump($demo);exit(1);
    // 6.为demo实例传递已初始化过的Dbh实例inst
    $inst = $demo->dbh($inst);
    // 7.数据增删除改查时常用属性赋值
    $matedata = $demo->matedata(['table'=>$table, 'primaryKey'=>$primaryKey]);

    // 8.运行demo中的方法
    // 8.1 生成测试数据
    $affected_rows = $demo->ins_dats_test(1);
    $effect = $affected_rows?'成功':'失败';
    echo nl2br("$effect, 生成数据。".PHP_EOL);
    // 8.2 更新测试数据
    $affected_rows = $demo->upd_dats_test(1);
    $effect = $affected_rows?'成功':'失败';
    echo nl2br("$effect, 更新数据。".PHP_EOL);
    // 8.3 条件查询单条数据
    $rows = $demo->get_4_test($display=1);
    $effect = $rows?'成功':'失败';
    echo nl2br("$effect, 条件查询单条数据。".PHP_EOL);
    // 8.4 条件查询所有数据
    $rows = $demo->get_3_test($display=1);
    $effect = $rows?'成功':'失败';
    echo nl2br("$effect, 条件查询所有数据。".PHP_EOL);
    // 8.5 清除测试数据
    $rows = $demo->del_dats_test(1);
    $effect = $rows?'成功':'失败';
    echo nl2br("$effect, 清除数据。".PHP_EOL);

}

// 启动测试主入口
main();
