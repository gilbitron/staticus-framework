<?php

namespace Staticus;

use Illuminate\Support\Arr;
use Spatie\DataTransferObject\DataTransferObject;

class Page extends DataTransferObject
{
    public string $path = '';

    public string $title = '';

    public array $frontMatter = [];

    public string $markdown = '';

    public string $html = '';

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getFrontMatter($key, $default = null)
    {
        return Arr::get($this->frontMatter, $key, $default);
    }
}
