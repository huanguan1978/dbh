<?php
declare (strict_types = 1);

// namespace app\facade;
namespace Dbh\thinkphp\facade;


class DbhFacade extends \think\Facade
{

    // getFacadeClass: 获取当前Facade对应类名
    protected static function getFacadeClass()
    {   
        // 返回当前类代理的类，这里返回容器已注册好的dbh实例
        return 'dbh';
    }

// cls.end
}