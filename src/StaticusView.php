<?php

namespace Staticus;

use Illuminate\Support\Arr;

class StaticusView
{
    /**
     * @var string
     */
    protected $environment;

    /**
     * @var array
     */
    protected $config;

    public function __construct($environment, $config)
    {
        $this->environment = $environment;
        $this->config      = $config;
    }

    /**
     * @return string
     */
    public function environment()
    {
        return $this->environment;
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function config($key = null, $default = null)
    {
        if (!$key) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function content($type)
    {
        return $this->config("content.{$type}");
    }
}
