<?php

namespace Staticus\Compilers;

use Staticus\Collection;

abstract class Compiler
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var string|null
     */
    public $singleView = null;

    /**
     * @var string|null
     */
    public $collectionView = null;

    /**
     * @var int
     */
    public $perPage = 10;

    /**
     * @var \Staticus\Collection
     */
    protected $collection;

    private function __construct()
    {
        //
    }

    /**
     * @param string $path
     * @return self
     */
    public static function create($path)
    {
        $obj = new static();

        $obj->path       = $path;
        $obj->collection = new Collection();

        return $obj;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function singleView($template)
    {
        $this->singleView = $template;

        return $this;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function collectionView($template)
    {
        $this->collectionView = $template;

        return $this;
    }

    /**
     * @param int $perPage
     * @return $this
     */
    public function perPage(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return $this
     */
    abstract public function fetchContent();

    /**
     * @param string $key
     * @param string $direction
     * @return $this
     */
    public function sortByFrontMatter($key, $direction = 'asc')
    {
        $this->collection->sortByFrontMatter($key, $direction);

        return $this;
    }

    /**
     * @return \Staticus\Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return ltrim(rtrim(str_replace('{slug}', '', $this->path), '/'), '/');
    }
}
