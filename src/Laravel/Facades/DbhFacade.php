<?php
 
namespace Dbh\Laravel\Facades;
 
use \Illuminate\Support\Facades\Facade;
 
class DbhFacade extends Facade {
    protected static function getFacadeAccessor() {
        return 'dbh';
    }
}