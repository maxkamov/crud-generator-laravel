<?php

namespace Maxkamov48\CrudGeneratorLaravel\Generator;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Maxkamov48\CrudGeneratorLaravel\Generator\Contracts\FileTemplateInterface;

class CoreGenerator
{
    protected Filesystem $files;

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function fileExists(string $path): bool
    {
        return $this->files->exists($path);
    }

    public function execute(FileTemplateInterface $baseFile): void
    {
        $fileContent = $this->performReplacements($baseFile->getReplacements(), $baseFile->getStub());

        $this->writeToFile($baseFile->_getPath(), $fileContent);
    }

    protected function performReplacements($replace, $stubFile): string
    {
        return str_replace(
            array_keys($replace), array_values($replace), $stubFile
        );
    }

    protected function writeToFile($path, $content): void
    {
        $this->makeDirectory($path);

        $this->files->put($path, $content);
    }

    protected function makeDirectory(string $path): string
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
        return $path;
    }

}
