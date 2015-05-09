<?php namespace Tx;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Factory;

class ViewProvider extends ServiceProvider{

    /**
     * Array containg paths where to look for blade files
     * @var array
     */
    public $viewPaths;

    /**
     * Location where to store cached views
     * @var string
     */
    public $cachePath;

    /**
     * @var Illuminate\Container\Container
     */
    protected $container;

    /**
     * Initialize class
     * @param array  $viewPaths
     * @param string $cachePath
     */
    public function __construct($conf) {
        $this->viewPaths = $conf['viewPaths'];
        $this->cachePath = $conf['cachePath'];

        $this->container = new Container;
        $this->registerFilesystem();
        $this->registerEvents();

        $this->register();
    }
    public function register() {
        $this->registerEngineResolver();
        $this->registerViewFinder();
    }

    public function registerFilesystem()
    {
        $this->container->bindShared('files', function(){
            return new Filesystem;
        });
    }
    public function registerEvents()
    {
        $this->container->bindShared('events', function(){
            return new Dispatcher;
        });
    }
    /**
     * Register the engine resolver instance.
     *
     * @return void
     */
    public function registerEngineResolver()
    {
        $this->container->bindShared('view.engine.resolver', function()
        {
            $resolver = new EngineResolver;

            // Next we will register the various engines with the resolver so that the
            // environment can resolve the engines it needs for various views based
            // on the extension of view files. We call a method for each engines.
            foreach (array('php', 'blade') as $engine)
            {
                $this->{'register'.ucfirst($engine).'Engine'}($resolver);
            }

            return $resolver;
        });
    }

    /**
     * Register the PHP engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerPhpEngine($resolver)
    {
        $resolver->register('php', function() { return new PhpEngine; });
    }

    /**
     * Register the Blade engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerBladeEngine($resolver)
    {
        $me = $this;

        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Blade compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $this->container->bindShared('blade.compiler', function() use ($me)
        {
            $cache = $me->cachePath;

            return new BladeCompiler($me->container['files'], $cache);
        });

        $resolver->register('blade', function() use ($me)
        {
            return new CompilerEngine($me->container['blade.compiler'], $me->container['files']);
        });
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $me = $this;
        $this->container->bindShared('view.finder', function() use ($me)
        {
            $paths = $me->viewPaths;

            return new FileViewFinder($me->container['files'], $paths);
        });
    }

    /**
     * Register the view environment.
     *
     * @return void
     */
    public function registerFactory()
    {
        // Next we need to grab the engine resolver instance that will be used by the
        // environment. The resolver will be used by an environment to get each of
        // the various engine implementations such as plain PHP or Blade engine.
        $resolver = $this->container['view.engine.resolver'];

        $finder = $this->container['view.finder'];

        $env = new Factory($resolver, $finder, $this->container['events']);

        // We will also set the container instance on this view environment since the
        // view composers may be classes registered in the container, which allows
        // for great testable, flexible composers for the application developer.
        $env->setContainer($this->container);

        return $env;
    }

    /**
     * @brief getInstance
     *
     * @return
     */
    public function getInstance(){
        return $this->registerFactory();
    }

}
