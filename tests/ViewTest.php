<?php
use \Tx\ViewProvider;
use \Tx\View as TxView;

class ViewTest extends TestCase{

    public function testView(){
        $v = (new ViewProvider([
            'viewPaths' => [__DIR__ . '/views'],
            'cachePath' => __DIR__ . '/cache',
        ]))->getInstance();
        echo $v->make('hello');
    }

    public function testFacade(){
        echo View::make('hello');
    }
}

class View extends TxView{
    public static function conf(){
        return [
            'viewPaths' => [__DIR__ . '/views'],
            'cachePath' => __DIR__ . '/cache',
        ];
    }
}

