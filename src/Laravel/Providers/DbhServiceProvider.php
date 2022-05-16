<?php

namespace Dbh\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

use Dbh\Dbh;

class DbhServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('dbh', function(){
            return new Dbh();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $inst = app('dbh');

        $logpath = storage_path().DIRECTORY_SEPARATOR.'logs';
        $inst->_fslog($logpath); // fslog object init

        $inst = app('dbh');
        $link = $inst->_link_lv6db();
        if($link){
            $inst->dbh('rw', $link['linker'], $link['driver'], $link['optional']);
            $inst->_dblog($link['driver']); // dblog object init
        }

    }
}
