<?php

namespace Ashiful\Exco\Commands;

use Ashiful\Exco\Support\GenerateFile;
use Ashiful\Exco\Support\FileGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str;

class CreateRepositoryCommand extends CommandGenerator
{
    public $argumentName = 'repository';

    protected $name = 'make:repo';

    protected $description = 'Command for repository';

    public function __construct()
    {
        parent::__construct();
    }

    protected function getArguments(): array
    {
        return [
            ['repository', InputArgument::REQUIRED, 'The name of the repository class.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['interface', 'i', InputOption::VALUE_NONE, 'Flag to create associated Interface', null]
        ];
    }

    private function getRepositoryName(): string
    {
        $repository = Str::studly($this->argument('repository'));

        if (Str::contains(strtolower($repository), 'repository') === false) {
            $repository .= 'Repository';
        }

        return $repository;
    }

    private function resolveNamespace(): string
    {
        if (strpos($this->getServiceNamespaceFromConfig(), self::APP_PATH) === 0) {
            return str_replace(self::APP_PATH, '', $this->getServiceNamespaceFromConfig());
        }
        return '/' . $this->getServiceNamespaceFromConfig();
    }

    protected function getDestinationFilePath(): string
    {
        return app_path() . $this->resolveNamespace() . '/Repositories/Eloquent' . '/' . $this->getRepositoryName() . '.php';
    }

    protected function getInterfaceName(): string
    {
        return $this->getRepositoryName() . "Interface";
    }
    protected function interfaceDestinationPath(): string
    {
        return app_path() . $this->resolveNamespace() . "/Repositories" . '/' . $this->getInterfaceName() . '.php';
    }

    private function getRepositoryNameWithoutNamespace(): string
    {
        return class_basename($this->getRepositoryName());
    }

    public function getDefaultNamespace(): string
    {
        $configNamespace = $this->getRepositoryNamespaceFromConfig();
        return "$configNamespace\\Repositories";
    }
    private function getInterfaceNameWithoutNamespace(): string
    {
        return class_basename($this->getInterfaceName());
    }

    public function getDefaultInterfaceNamespace(): string
    {
        $configNamespace = $this->getRepositoryNamespaceFromConfig();
        return "$configNamespace\\Repositories";
    }

    protected function getStubFilePath(): string
    {
        if ($this->option('interface') === true) {
            $stub = '/stubs/repository-interface.stub';
        } else {
            $stub = '/stubs/repository.stub';
        }

        return $stub;
    }

    protected function getTemplateContents(): string
    {
        return (new GenerateFile(__DIR__ . $this->getStubFilePath(), [
            'CLASS_NAMESPACE' => $this->getClassNamespace(). '\\Eloquent',
            'INTERFACE_NAMESPACE' => $this->getInterfaceNamespace() . '\\' . $this->getInterfaceNameWithoutNamespace(),
            'CLASS' => $this->getRepositoryNameWithoutNamespace(),
            'INTERFACE' => $this->getInterfaceNameWithoutNamespace()
        ]))->render();
    }
    protected function getInterfaceTemplateContents(): string
    {
        return (new GenerateFile(__DIR__ . "/stubs/interface.stub", [
            'CLASS_NAMESPACE' => $this->getInterfaceNamespace(),
            'INTERFACE' => $this->getInterfaceNameWithoutNamespace()
        ]))->render();
    }

    public function handle()
    {
        $path = str_replace('\\', '/', $this->getDestinationFilePath());

        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getTemplateContents();

        // For Interface
        if ($this->option('interface') == true) {
            $interfacePath = str_replace('\\', '/', $this->interfaceDestinationPath());

            if (!$this->laravel['files']->isDirectory($dir = dirname($interfacePath))) {
                $this->laravel['files']->makeDirectory($dir, 0777, true);
            }

            $interfaceContents = $this->getInterfaceTemplateContents();
        }

        try {
            (new FileGenerator($path, $contents))->generate();

            $this->info("Created : {$path}");

            // For Interface
            if ($this->option('interface') === true) {

                (new FileGenerator($interfacePath, $interfaceContents))->generate();

                $this->info("Created : {$interfacePath}");
            }

        } catch (\Exception $e) {

            $this->error("File : {$e->getMessage()}");

            return E_ERROR;
        }

        return 0;

    }

}
