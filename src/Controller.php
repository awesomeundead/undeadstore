<?php

namespace Awesomeundead\Undeadstore;

use League\Plates\Engine;

class Controller
{
    protected $templates;

    public function __construct(Engine $templates)
    {
        $this->templates = $templates;
        $this->templates->setDirectory(ROOT . '/app/views');
    }
}