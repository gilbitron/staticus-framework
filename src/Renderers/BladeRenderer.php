<?php

namespace Staticus\Renderers;

class BladeRenderer implements Renderer
{
    /**
     * @var string
     */
    protected $viewsPath;

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * @var \Illuminate\View\Factory
     */
    protected $viewFactory;

    /**
     * @param string|null $viewsPath
     * @param string|null $cachePath
     * @return void
     */
    public function __construct($viewsPath, $cachePath)
    {
        $this->viewsPath = $viewsPath;
        $this->cachePath = $cachePath;

        $this->setUpBlade();
    }

    /**
     * @return void
     */
    protected function setUpBlade()
    {
        $container = \Illuminate\Container\Container::getInstance();

        $container->instance(\Illuminate\Contracts\Foundation\Application::class, $container);

        $filesystem      = new \Illuminate\Filesystem\Filesystem;
        $eventDispatcher = new \Illuminate\Events\Dispatcher($container);

        $viewResolver  = new \Illuminate\View\Engines\EngineResolver;
        $bladeCompiler = new \Illuminate\View\Compilers\BladeCompiler($filesystem, $this->cachePath);

        $viewResolver->register('blade', function () use ($bladeCompiler) {
            return new \Illuminate\View\Engines\CompilerEngine($bladeCompiler);
        });

        $viewFinder        = new \Illuminate\View\FileViewFinder($filesystem, [$this->viewsPath]);
        $this->viewFactory = new \Illuminate\View\Factory($viewResolver, $viewFinder, $eventDispatcher);
        $this->viewFactory->setContainer($container);
        \Illuminate\Support\Facades\Facade::setFacadeApplication($container);
        $container->instance(\Illuminate\Contracts\View\Factory::class, $this->viewFactory);
        $container->alias(
            \Illuminate\Contracts\View\Factory::class,
            (new class extends \Illuminate\Support\Facades\View {
                public static function getFacadeAccessor()
                {
                    return parent::getFacadeAccessor();
                }
            })::getFacadeAccessor()
        );
        $container->instance(\Illuminate\View\Compilers\BladeCompiler::class, $bladeCompiler);
        $container->alias(
            \Illuminate\View\Compilers\BladeCompiler::class,
            (new class extends \Illuminate\Support\Facades\Blade {
                public static function getFacadeAccessor()
                {
                    return parent::getFacadeAccessor();
                }
            })::getFacadeAccessor()
        );
    }

    /**
     * @param string $view
     * @param array $data
     * @return string
     */
    public function render($view, $data = [])
    {
        return $this->viewFactory->make($view, $data)->render();
    }
}
