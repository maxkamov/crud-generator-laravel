<?php

namespace Maxkamov48\CrudGeneratorLaravel\Generator;


use Maxkamov48\CrudGeneratorLaravel\Generator\Contracts\FileTemplateInterface;

class BaseModelFile extends BaseTemplateFile implements FileTemplateInterface
{
    public function getReplacements()
    {
        return [
            '{{modelNamespace}}' => $this->getNamespace(),
            '{{modelName}}' => $this->name,
            '{{fillable}}' => $this->getFillables(),
            '{{relations}}' => $this->getRelations(),
        ];
    }

    public static function create($data): self {
        return new self($data['name'], $data['namespace'], $data['stubFilePath']);
    }

    private function getFillables(){
        return '';
    }

    private function getRelations(){
        return '';
    }
}
