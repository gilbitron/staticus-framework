<?php

namespace Staticus;

use Illuminate\Support\Collection as IlluminateCollection;
use Staticus\Page;

class Collection
{
    /**
     * @var \Illuminate\Support\Collection
     */
    public $items;

    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = new IlluminateCollection($items);
    }

    /**
     * @param string $key
     * @param string $direction
     * @return $this
     */
    public function sortByFrontMatter(string $key, string $direction = 'asc')
    {
        $this->items = $direction === 'asc' ?
            $this->items->sortBy(function (Page $page) use ($key) {
                return $page->getFrontMatter($key);
            }) :
            $this->items->sortByDesc(function (Page $page) use ($key) {
                return $page->getFrontMatter($key);
            });

        return $this;
    }

    /**
     * @return $this
     */
    public function push(Page $page)
    {
        $this->items->push($page);

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items->all();
    }

    /**
     * @param int $page
     * @param string $path
     * @param int $perPage
     * @return object
     */
    public function pagination($page = 1, $path = '', $perPage = 10)
    {
        $items = $this->items->forPage($page, $perPage);

        return (object) [
            'items'         => $items->all(),
            'currentPage'   => $page,
            'perPage'       => $perPage,
            'lastPage'      => $this->totalPages($perPage),
            'total'         => $this->items->count(),
            'path'          => $path,
            'firstPagePath' => "{$path}/page/1",
            'lastPagePath'  => "{$path}/page/{$this->totalPages($perPage)}",
            'nextPagePath'  => $page < $this->totalPages($perPage) ? $path . '/page/' . ($page + 1) : null,
            'prevPagePath'  => $page > 1 ? $path . '/page/' . ($page - 1) : null,
        ];
    }

    /**
     * @param int $perPage
     * @return int
     */
    public function totalPages($perPage)
    {
        return (int) ceil($this->items->count() / $perPage);
    }
}
