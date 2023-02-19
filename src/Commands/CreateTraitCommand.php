<?php

namespace Ashiful\Exco\Commands;

use Ashiful\Exco\Support\GenerateFile;
use Ashiful\Exco\Support\FileGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;

class CreateTraitCommand extends CommandGenerator
{
    public $argumentName = 'trait';

    protected $name = 'make:trait';

    protected $description = 'make trait file';

    protected function getArguments(): array
    {
        return [
            ['trait', InputArgument::REQUIRED, 'The name of the trait'],
        ];
    }

    public function __construct()
    {
        parent::__construct();
    }

    private function getTraitName(): string
    {
        return Str::studly($this->argument('trait'));
    }

    protected function getDestinationFilePath(): string
    {
        return app_path() . "/Traits" . '/' . $this->getTraitName() . '.php';
    }

    private function getTraitNameWithoutNamespace(): string
    {
        return class_basename($this->getTraitName());
    }

    public function getDefaultNamespace(): string
    {
        return "App\\Traits";
    }

    protected function getStubFilePath(): string
    {
        return '/stubs/traits.stub';
    }

    protected function getTemplateContents(): string
    {
        return (new GenerateFile(__DIR__ . $this->getStubFilePath(), [
            'CLASS_NAMESPACE' => $this->getClassNamespace(),
            'CLASS' => $this->getTraitNameWithoutNamespace()
        ]))->render();
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
