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
     * @var \Staticus\Collection
     */
    protected $collection;

    /**
     * @param string $path
     * @return void
     */
    public function __construct($path)
    {
        $this->path       = $path;
        $this->collection = new Collection();
    }

    /**
     * @return $this
     */
    abstract public function getContent();

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
