<?php

namespace Staticus\Compilers;

use Parsedown;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Staticus\Page;
use Symfony\Component\Finder\Finder;

class MarkdownCompiler extends Compiler
{
    /**
     * @var string
     */
    protected $contentPath;

    /**
     * @param string $path
     * @param string $contentPath
     * @return void
     */
    public function __construct($path, $contentPath)
    {
        parent::__construct($path);

        $this->contentPath = $contentPath;
    }

    /**
     * @return $this
     */
    public function getContent()
    {
        $finder = new Finder();
        $files  = $finder->files()
            ->in($this->contentPath)
            ->ignoreDotFiles(true)
            ->name(['*.md', '*.markdown']);

        $parsedown = new Parsedown();

        foreach ($files as $file) {
            $content = file_get_contents($file->getPathname());
            $object  = YamlFrontMatter::parse($content);

            $slug = basename($file->getPathname(), '.' . $file->getExtension());
            $slug = $slug === 'index' ? '' : $slug;

            $path = str_replace('{slug}', $slug, $this->path);
            $path = ltrim(rtrim($path, '/'), '/');

            $this->collection->push(
                new Page([
                    'path'       => $path,
                    'title'      => $object->matter()['title'] ?? $slug,
                    'fontMatter' => $object->matter(),
                    'markdown'   => $object->body(),
                    'html'       => $parsedown->text($object->body()),
                ])
            );
        }

        return $this;
    }
}
