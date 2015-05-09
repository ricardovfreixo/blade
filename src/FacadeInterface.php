<?php namespace Tx;

interface FacadeInterface{
    // conf return config like this:
    /*
    [
        'viewPaths' => [''],
        'cachePath' => '',
    ]
    //*/

    public static function conf();
    public static function __callStatic($name, array $args);
}

