<?php
namespace Ashiful\Exco\Commands;

use Ashiful\Exco\Support\GenerateFile;
use Ashiful\Exco\Support\FileGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;

class CreateServiceCommand extends CommandGenerator
{
    public $argumentName = 'service';

    protected $name = 'make:service';

    protected $description = 'New service create command';

    protected function getArguments(): array
    {
        return [
            ['service', InputArgument::REQUIRED, 'The name of the service class.'],
        ];
    }


    public function __construct()
    {
       parent::__construct();
    }

    private function getServiceName(): string
    {
        $service = Str::studly($this->argument('service'));

        if (Str::contains(strtolower($service), 'service') === false) {
            $service .= 'Service';
        }

        return $service;
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
        return app_path() . $this->resolveNamespace() .'/Services'.'/'. $this->getServiceName() . '.php';
    }

    private function getServiceNameWithoutNamespace(): string
    {
        return class_basename($this->getServiceName());
    }

    public function getDefaultNamespace() : string
    {
        $configNamespace = $this->getServiceNamespaceFromConfig();
        return "$configNamespace\\Services";
    }
    protected function getStubFilePath(): string
    {
        return '/stubs/service.stub';
    }

    protected function getTemplateContents(): string
    {
        return (new GenerateFile(__DIR__.$this->getStubFilePath(), [
            'CLASS_NAMESPACE'   => $this->getClassNamespace(),
            'CLASS'             => $this->getServiceNameWithoutNamespace()
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
