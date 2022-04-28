<?php

namespace Dbh\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use Dbh\Dbh;

class ValidateTest extends TestCase {
    protected $dbm; // Dbh 实例


    function setUp():void {
        $this->dbm = new Dbh();
     }


     function testValidate1():void {
        $rule = [
            'username'=>['required','alpha_numeric',],
            'password'=>['required','between_len'=>[6,16], ],
            ];
        $fltr = [
            'username'=>['trim','strtolower','sanitize_string',],
            'password'=>['trim', ],	
        ];
        $emsg = [
            'username'=>['required'=>'必填用户姓名', 'alpha_numeric'=>'用户姓名仅可用字母数字组合', ],
            'password'=>['required'=>'必填用户密码', 'between_len'=>'用户密码长度仅限6至16位',],	
        ];
        
        $data = [
            'username'=>'MyNameIs',
            'password'=>'123456',
        ];

        $result = $this->dbm->validate($data, $rule, $fltr, $emsg);

        $this->assertSame(1, $result[0]);
        $this->assertSame(strtolower($data['username']), $result[1]['username']);
        $this->assertSame($data['password'], $result[1]['password']);

    }

    //cls.end
}
