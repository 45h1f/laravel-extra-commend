<?php

namespace Ashiful\Exco\Commands;

use Illuminate\Console\Command;

abstract class CommandGenerator extends Command
{
    public const APP_PATH = 'App';

    public $argumentName;

    abstract protected function getTemplateContents(): string;

    abstract protected function getDestinationFilePath(): string;

    public function getRepositoryNamespaceFromConfig(): string
    {
        return 'App';
    }

    public function getServiceNamespaceFromConfig(): string
    {
        return 'App';
    }

    public function getDefaultNamespace(): string
    {
        return '';
    }

    public function getDefaultInterfaceNamespace(): string
    {
        return '';
    }

    public function getClass(): string
    {
        return class_basename($this->argument($this->argumentName));
    }

    public function getClassNamespace(): string
    {
        $extra = str_replace(array($this->getClass(), '/'), array('', '\\'), $this->argument($this->argumentName));

        $namespace = $this->getDefaultNamespace();

        $namespace .= '\\' . $extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }

    public function getInterfaceNamespace(): string
    {
        $extra = str_replace(array($this->getClass() . 'Interface', '/'), array('', '\\'), $this->argument($this->argumentName) . 'Interface');
        $namespace = $this->getDefaultInterfaceNamespace();


        $namespace .= '\\' . $extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }


}
