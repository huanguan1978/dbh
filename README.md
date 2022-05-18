# DataBaseHandle 跨框架的数据库句柄

## 简述
当你不得不在多个PHP框架下为同一个项目编程时，你会发现80%的时间精力都在不同的PHP框架下写数据处理逻辑，在同一个需求开发涉汲多框架多端开发时，如：用laravel作API对接ios、android；用thinkphp作PC版前端和后台开发或作API对接miniprogram；你会发现你的CRUD编码时间会成倍增长且编码后的调试时间也会成倍增长。
提升代码复用率就是最好的解决之道，来吧，把数据处理从框架分离出来，用扩展库的方法引入项目。

Dbh类为常用PHP框架Thinkphp(5.0,5.1,6)，Laravel(6TLS)，Codeigniter(4)，已封装好ServiceProvider，即可在框架中集成也可脱离框架使用。

### 用dbh来处理数据
-   dbh实例集成了简洁高效的数据类Medoo
    ```php
        // 从框架容器中取出dbh单例
        $dbi = service('dbh');
        // 从dbi实例中取出只读实例
        $ro = $dbi->$dbh('ro');
        // 从dbi实例中取出读写实例，默认参rw为读写实例
        $rw = $dbi->$dbh();
    ```
-   dbh实例增删改查操作示例
    ```php
        // CRUD，获取单条记录    
        $row = $ro->get('_plog', '*', ['id'=>1,] );
        echo $ro->last(); // SELECT * FROM `_plog` WHERE `id` = 1 LIMIT 1

        // CRUD，获取多条记录
        $rows = $ro->select('_plog', '*', ['severity'=>'INFO',] );
        echo $ro->last(); // SELECT * FROM `_plog` WHERE `severity` = 'INFO'
        $rows = $ro->select('_plog', ['id','time','severity','message'], ['severity'=>'INFO',] );
        echo $ro->last(); // SELECT  `id`,`time`,`severity`,`message` FROM `_plog` WHERE `severity` = 'INFO'

        // CRUD，添加记录，若需用SQL原生函数可用raw函数处理
        $dat = ['time'=>$rw->raw('NOW()'), 'severity'=>'INFO', 'message'=>'info1', ];
        $pdostmt = $rw->insert('_plog', $dat);
        echo $rw->last(); // INSERT INTO `_plog` (`time`, `severity`, `message`) VALUES (NOW(), 'INFO', 'info1')
        // CRUD，获最记录的自增ID
        $id = $rw->id();

        // CRUD，更新记录，若需用SQL原生函数可用raw函数处理
        $dat = ['time'=>$rw->raw('NOW()'), 'severity'=>'INFO', 'message'=>'info111', ];
        $pdostmt = $rw->update('_plog', $dat, ['id'=>1]);
        echo $rw->last(); // UPDATE `_plog` SET `time` = NOW(), `message` = 'info111' WHERE `id` = '1'

        // CRUD，删除记录
        $pdostmt = $rw->delete('_plog', ['id'=>1]);
        echo $rw->last(); // DELETE FROM `_plog` WHERE `id` = '1'

        // 怎样知道是否错，判断实例属性error是否有值, 实例属性errorInfo得到出错细节
        if($rw->error){
            print_r($rw->errorInfo);
        }

        // 多表联查，数据排序，数据分页，批量操作，事务处理，等进阶用法更多细节请查阅Medoo官方文档
        // 脱离框架使用dbh请参考 demo/Dbt2.php
    ```

### 用dbh来记录日志
-   dbh实例集成了简洁高效且兼容PR-3的日志类Plog，默认日志表为_plog
    ```php
        // 从框架容器中取出dbh单例
        $dbi = service('dbh');
        // 从dbi实例中取出Plog实例，默认参db为数据库表记录日志
        $log = $dbi->$wlog('db');
    ```
-   dbh实例增删除改查操作示例    
    ```php
        // 6个日志级别，info, notice, debug, warning, error, fatal，均支持快捷操作，必填参数1为文本日志信息，可选参数2为附带数据
        $plog->info('HelloWorld');
        $plog->info('HelloWorld', ['data'=>[123,]]);
        $plog->debug("I'mHere",['data'=>['123','456',"I'm"]]);        
    ```
-   plog实例输出日志如下
    ```sql
    SELECT * FROM _plog;
    ```
    | **id** | **time** | **path** | **line** | **severity** | **message** | **context** |
    | --- | --- | --- | --- | --- | --- | --- |
    | 1 | 2022-04-16 16:29:30 | test2.localhost.localdomain/xz/plog/demo.php | 25 | INFO | HelloWorld |  |
    | 2 | 2022-04-16 16:29:30 | test2.localhost.localdomain/xz/plog/demo.php | 26 | INFO | HelloWorld | {"data":\[123\]} |
    | 3 | 2022-04-16 16:29:30 | test2.localhost.localdomain/xz/plog/demo.php | 26 | DEBUG | ImHere | {"data":\["123","456","I\\u0027m"\]} |

### 框架引入Dbh功能
    常用PHP框架Thinkphp(5.0,5.1,6)，Laravel(6TLS)，Codeigniter(4)，Dbh已封装好ServiceProvider，Facade, Middleware, Event框架中引入即可。

#### 引入Dbh服务提供者
 - CodeIgniter 4, Config\Autoload.php
    ```php
    public $psr4 = [
        APP_NAMESPACE => APPPATH, // For custom app namespace
        'Config'      => APPPATH . 'Config',
        'Dbh\\CodeIgniter' => ROOTPATH.'vendor\\orz\\dbh\\src\\CodeIgniter\\',
    ];
    ```
 - Laravel 6，config/app.php
    ```php
    'providers' => [
        //My Dbh Service Providers...
        Dbh\Laravel\Providers\DbhServiceProvider::class,
    ],
    'aliases' => [
        // My Dbh Facade...
        'Dbf' => Dbh\Facades\DbhFacade::class,
    ]       
    ```

 - Thinkphp 6，app/service.php
    ```php
        <?php
        use app\AppService;
        return [
            AppService::class,
            // myService
            \Dbh\thinkphp\service\DbhService::class,
        ]; 
    ```
 - Thinkphp 5.1，application/provider.php
    ```php
    <?php
    return [
        'dbh'=>Dbh\thinkphp\provider\DbhProvider::class
    ];
    ```
 - Thinkphp 5.0，可以通过Request类的单例模式进行注入，用助手函数request()取出实例
    ```php
    <?php
    namespace app\index\controller;

    use think\Request;
    use Dbh\thinkphp\provider\DbhProvider;

    class Index extends Controller {

        protected $dbi = null;

        function _initialize(){                
            $this->dbi = DbhProvider::invoke($this->request);
            parent::_initialize();
        }
        /**
         * 测试1，属性注入后用助手函数request()调用
         * http://test12.localhost.localdomain/index/index/test1
         */
        function test1(){
            $result = 'HelloThinkphp50';

            $rw = request()->dbh();
            $pdostmt = $rw->query('SELECT version() as version');
            $version = $pdostmt->fetchAll();
            var_dump($version); // [['version':'5.7.1']]

            $log = $this->dbi->wlog('db');
            $log->info($result);

            return $result;
        }
    }
    ```

#### 引入Dbh的Facade
    Thinkphp 5.1, Thinkphp 6, Laravel 6，这些框架的静态代理均用的是服务容器的单例模式，和使用服务容器一样的性能

 - Laravel 6，config/app.php
    ```php
    use Dbh\Laravel\Facades\DbhFacade as Dbf;
    Dbf::wlog('db')->info('helloworld,1234'); 
    $podstmt = Dbf::dbh()->query('SELECT version() AS version');
    print_r($podstmt->fetchAll());
    ```

 - Thinkphp 6 and 5.1，app/service.php
    ```php
    use Dbh\thinkphp\facade\DbhFacade as Dbf;
    Dbf::wlog('db')->info('helloworld,1234');     
    $podstmt = Dbf::dbh()->query('SELECT version() AS version');
    print_r($podstmt->fetchAll());
    ```

#### 引入Dbh中间件
    为了方便调试程序，Dbh已封装好了如下几个功能的中间件，Thinkphp5.1,Thinkphp6,Laravel6,均已测试且在项目中真实应用过。
    中间件，PlogBefore，通过数据表记录HTTP请时时的PHP超全局变量 $_SERVER, $_REQUEST, $_SESSION, $_ENV, $_FILES, 以及全部请求头信息。 
    中间件，PlogAfter， 通过数据表记录HTTP响应时的PHP超全局变量 $_SESSION, 响应正文，以及全部响应头信息。
    注：慢页面监控功能，记录超过3秒加载时间的页面，也是在PlogAfter中实现记录的。 

#### 引入Dbh事件监听
    为了方便调试程序，Dbh已封装好了如下几个功能的事件，Thinkphp5.0,Thinkphp5.1,Thinkphp6,Laravel6,CodeIgniter4均已测试且在项目中真实应用过。
    事件名，SessionChange，通过数据表记录当前的会话变量数据。
    事件名，CookieChange， 通过数据表记录当前的Cookie数据。
    事件名，EnvChange，    通过数据表记录当前的环境变量数据。
    事件名，GlobalsChange，通过数据表记录当前的全局变量数据。
    事件名，ConfigChange， 通过数据表记录当前的配置文件数据。

#### 更多技术细节可参考

[用Composer为Framework引入Loaclhost第三方代码库](https://github.com/huanguan1978/skills/blob/main/FrameworkExtend.md)

[脱离框架用Dbh类处理数据代码示例](https://github.com/huanguan1978/dbh/blob/main/src/demo/Dbt2.php)

[Medoo官方文档2.1版](https://medoo.in/doc)
[Medoo中文手册1.6版](https://medoo.lvtao.net/1.2/doc.php)


### 用composer安装最新版本
``` shell
$ composer require orz/dbh
```

### 不用composer直接下载
[Download ZIP](https://github.com/huanguan1978/dbh/archive/refs/heads/main.zip) See demo/Dbt2.php.


#### 致谢
感谢 Thinkphp, Laravel, CodeIgniterMedoo, SimplePhpLogger, GUMP, 这些优秀类库的作者，是你们类库方便了日常编程开发。
