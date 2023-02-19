<?php

namespace Ashiful\Exco\Support;

class GenerateFile
{

    protected $path;

    protected static $basePath = '';
    protected $replaces = [];

    public function __construct(string $path, array $replaces = [])
    {
        $this->path = $path;
        $this->replaces = $replaces;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getContents(): string
    {
        $contents = file_get_contents($this->getPath());

        foreach ($this->replaces as $search => $replace) {
            $contents = str_replace('$' . strtoupper($search) . '$', $replace, $contents);
        }

        return $contents;
    }

    public function render(): string
    {
        return $this->getContents();
    }

}
