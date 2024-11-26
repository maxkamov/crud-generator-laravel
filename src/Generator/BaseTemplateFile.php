<?php

namespace Maxkamov48\CrudGeneratorLaravel\Generator;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Maxkamov48\CrudGeneratorLaravel\Generator\Contracts\FileTemplateInterface;

class BaseTemplateFile implements FileTemplateInterface
{
    protected Filesystem $files;

    public string $name = 'SomeController';

    public string $namespace = 'App\Http\Controllers\Api';
    public string $stubFilePath = 'api/Controller';

    /**
     * @param string $name
     * @param string $namespace
     * @param string $stubFilePath
     */
    public function __construct(string $name, string $namespace, string $stubFilePath)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->stubFilePath = $stubFilePath;
        $this->files = new Filesystem();
    }

    public static function create($data): self {
        return new self($data['name'], $data['namespace'], $data['stubFilePath']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getStubFilePath(): string
    {
        return $this->stubFilePath;
    }

    public function getReplacements()
    {
        return [
            '{{fillable}}' => 'Some string',
        ];
    }

    public function getStub(string $type = null, bool $content = true): string
    {
        $type = $this->stubFilePath;
        $stub_path = config('crud.stub_path', 'default');

        if (blank($stub_path) || $stub_path == 'default') {
            $stub_path = __DIR__.'/../stubs/';
        }

        $path = Str::finish($stub_path, '/')."$type.stub";

        if (! $content) {
            return $path;
        }

        return $this->files->get($path);
    }

    public function _getPath(): string
    {
        return app_path($this->_getNamespacePath()."{$this->getName()}.php");
    }

    public function _getNamespacePath(): string
    {
        $str = Str::start(Str::finish(Str::after($this->getNamespace(), 'App'), '\\'), '\\');

        return str_replace('\\', '/', $str);
    }
}
