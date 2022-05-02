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

    /**
     * @var string
     */
    protected $outputDir;

    /**
     * @param string $environment
     * @param array $config
     * @param string $outputDir
     * @return void
     */
    public function __construct($environment, $config, $outputDir)
    {
        $this->environment = $environment;
        $this->config      = $config;
        $this->outputDir   = $outputDir;
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

    /**
     * @param mixed $path
     * @return mixed
     */
    public function asset($path)
    {
        $path = '/' . ltrim($path, '/');

        if (file_exists("{$this->outputDir}/mix-manifest.json")) {
            $manifest = json_decode(file_get_contents("{$this->outputDir}/mix-manifest.json"), true);

            if (isset($manifest[$path])) {
                return $manifest[$path];
            }
        }

        return $path;
    }
}
