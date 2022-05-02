<?php

namespace Staticus;

use Illuminate\Support\Str;
use Staticus\Compilers\Compiler;
use Staticus\Renderers\BladeRenderer;

class Staticus
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $outputDir;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Staticus\Renderers\Renderer
     */
    protected $renderer;

    /**
     * @param string $basePath
     * @param string $environment
     * @param string|null $outputDir
     * @return void
     */
    public function __construct($basePath, $environment = 'local', $outputDir = null)
    {
        $this->basePath    = $basePath;
        $this->environment = $environment;
        $this->outputDir   = $outputDir;
    }

    /**
     * @return void
     */
    public function build()
    {
        $this->renderer = new BladeRenderer(
            $this->basePath . '/views',
            $this->basePath . '/cache/blade'
        );

        $this->config = [];
        if (file_exists("{$this->basePath}/config.php")) {
            $this->config = require "{$this->basePath}/config.php";
        }
        if (file_exists("{$this->basePath}/config.{$this->environment}.php")) {
            $this->config = array_merge($this->config, require "{$this->basePath}/config.{$this->environment}.php");
        }

        $content = $this->config['content'] ?? [];

        $this->cleanOutput();

        foreach ($content as $contentKey => $contentItem) {
            if ($contentItem instanceof Page) {
                $view = $contentItem->view ?? $contentKey;

                $this->renderPage($contentItem, $view);
            } elseif ($contentItem instanceof Compiler) {
                $collection = $contentItem->collection();

                foreach ($collection->all() as $page) {
                    $this->renderPage($page, Str::singular($contentKey));
                }

                $this->renderPage(
                    new Page([
                        'path'  => $contentItem->getBasePath(),
                        'title' => 'Page 1',
                    ]),
                    $contentItem->singleView ?: $contentKey,
                    $collection->pagination(1, $contentItem->getBasePath(), $contentItem->perPage),
                );

                for ($i = 1; $i <= $collection->totalPages($contentItem->perPage); $i++) {
                    $this->renderPage(
                        new Page([
                            'path'  => $contentItem->getBasePath() . "/page/{$i}",
                            'title' => "Page {$i}",
                        ]),
                        $contentItem->collectionView ?: $contentKey,
                        $collection->pagination($i, $contentItem->getBasePath(), $contentItem->perPage),
                    );
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function cleanOutput()
    {
        $dir = $this->getOutputDir();

        if (!is_dir($dir)) {
            @mkdir($dir);

            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            if (
                Str::startsWith(str_replace($dir, '', $fileinfo->getPathname()), '/assets') ||
                Str::startsWith(str_replace($dir, '', $fileinfo->getPathname()), '/mix-manifest.json')
            ) {
                continue;
            }

            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            @$todo($fileinfo->getRealPath());
        }
    }

    /**
     * @param Page $page
     * @param string $view
     * @param object|null $pagination
     * @return void
     */
    protected function renderPage(Page $page, $view, $pagination = null)
    {
        $dir = rtrim($this->getOutputDir() . '/' . $page->path, '/');
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $output = $this->renderer->render($view, [
            'staticus'   => new StaticusView($this->environment, $this->config, $this->getOutputDir()),
            'page'       => $page,
            'pagination' => $pagination,
        ]);

        file_put_contents("{$dir}/index.html", $output);
    }

    /**
     * @return string
     */
    protected function getOutputDir()
    {
        if ($this->outputDir) {
            return realpath($this->outputDir);
        }

        return $this->basePath . '/dist';
    }
}
