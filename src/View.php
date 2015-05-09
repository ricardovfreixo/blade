<?php namespace Tx;

abstract class View implements FacadeInterface{
    protected static $factory;

    private function __construct(){}

    protected static function init(){
        if(self::$factory !== null){
            return;
        }
        self::$factory = new ViewProvider(static::conf());
    }

    public static function __callStatic($name, array $args){
        self::init();
        return call_user_func_array([self::$factory->getInstance(), $name], $args);
    }
}

