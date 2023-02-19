<?php

namespace Ashiful\Exco\Commands;

use Ashiful\Exco\Support\GenerateFile;
use Ashiful\Exco\Support\FileGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;

class CreateBladeCommand extends CommandGenerator
{
    public $argumentName = 'view';

    protected $name = 'make:view';

    protected $description = 'Make view file';

    public function getArguments(): array
    {
        return [
            ['view', InputArgument::REQUIRED, 'The name of the view'],
        ];
    }

    public function __construct()
    {
        parent::__construct();
    }

    private function getViewName(): string
    {
        $view = Str::camel($this->argument('view'));
        if (Str::contains(strtolower($view), '.blade.php') === false) {
            $view .= '.blade.php';
        }
        return $view;
    }

    protected function getDestinationFilePath(): string
    {
        return base_path() . "/resources/views" . '/' . $this->getViewName();
    }

    protected function getStubFilePath(): string
    {
        return '/stubs/blade.stub';
    }

    protected function getTemplateContents(): string
    {
        return (new GenerateFile(__DIR__ . $this->getStubFilePath()))->render();
    }

    public function handle()
    {
        $path = str_replace('\\', '/', $this->getDestinationFilePath());

        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getTemplateContents();

        try {
            (new FileGenerator($path, $contents))->generate();

            $this->info("Created : {$path}");
        } catch (\Exception $e) {

            $this->error("File : {$e->getMessage()}");

            return E_ERROR;
        }

        return 0;

    }

}
