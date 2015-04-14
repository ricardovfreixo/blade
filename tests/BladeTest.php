<?php
use \Tx\Blade;
class BladeTest extends TestCase{
    public $view;

    public function __construct(){
        $views = __DIR__ . '/views';
        $cache = __DIR__ . '/cache';
        $blade = new Blade($views, $cache);
        $this->view = $blade->getView();
    }

    public function testView(){
        echo $this->view->make('hello');
    }
}

