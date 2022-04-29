<?php

namespace Staticus\Renderers;

interface Renderer
{
    /**
     * @param string $view
     * @param array $data
     * @return string
     */
    public function render($view, $data = []);
}
