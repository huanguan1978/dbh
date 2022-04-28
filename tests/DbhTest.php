<?php
namespace Dbh\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use Dbh\Dbh;

use Plog\Plogger;

class DbhTest extends TestCase {
    protected $dbh;
    protected $log;
    protected $tablename;
    protected $tempfile;

    protected $dbm; // Dbh 实例
    protected $dbrw; // 封装过的medoo对象

    function setUp():void {
        $dsn = 'sqlite::memory:';
        $driver_name = 'sqlite';
        $this->tempfile = tempnam(sys_get_temp_dir(), $driver_name);
        // echo $tempfile;
        $dsn = sprintf('%s:%s',$driver_name,$this->tempfile);
        $this->dbh = new PDO($dsn);
        $this->tablename = 'plog';

        // var_dump($this->dbh);exit;
        $this->dbm = new Dbh();
        $this->dbrw = $this->dbm->dbh('rw', $this->dbh, $driver_name);

        // $this->log = new Plogger();
        $this->log = Plogger::getInstance();
        $this->log->setOptions(['logFormat' => 'Y-m-d H:i:s']);
        $this->log->dblink($this->dbh);
        $this->log->dbtype($driver_name);
        $this->log->dbtable($this->tablename, true);
    }

    function tearDown():void {
        $this->dbh = null;
        if(!empty($this->tempfile) && file_exists($this->tempfile) ){
            unset($this->dbh);
            @unlink($this->tempfile);
        }
    }

    function testLogInfo():void {
        $level = 'INFO';
        $message = 'helloworld';
        $content = 'HelloWorld';
        $data = ['level'=>$level, 'message'=>$message, 'content'=>$content];
        // $json = json_encode($data);
        $ok = $this->log->info($message, $data);
        $this->assertTrue($ok);
        if($ok){
            $id = $this->log->lastInsertId();
            $row = $this->log->dbfind($id);
            $this->assertNotEmpty($row);
            if($row){
                $this->assertSame($level, $row['severity']);
                $this->assertSame($message, $row['message']);
                // $this->assertSame($json, $row['context']);
            }
        }
    }

    function testDbhInfo():void {
        $level = 'DEBUG';
        $message = 'helloworld2';
        $content = 'HelloWorld2';
        $data = ['level'=>$level, 'message'=>$message, 'content'=>$content];
        // $json = json_encode($data);
        $ok = $this->log->debug($message, $data);
        $this->assertTrue($ok);
        if($ok){
            $id = $this->log->lastInsertId();
            $row = $this->log->dbfind($id);
            $this->assertNotEmpty($row);
            if($row){
                $this->assertSame($level, $row['severity']);
                $this->assertSame($message, $row['message']);
                // $this->assertSame($json, $row['context']);

                $rec = $this->dbrw->get($this->tablename, '*', ['id'=>$id] ); // return array
                $this->assertNotEmpty($rec);
                if($rec){
                    $this->assertSame($level, $rec['severity']);
                    $this->assertSame($message, $rec['message']);    
                }
                
            }
        }
    }

    //cls.end
}
