<?php

namespace Maxkamov48\CrudGeneratorLaravel\Generator;


use Maxkamov48\CrudGeneratorLaravel\Generator\Contracts\FileTemplateInterface;
use Maxkamov48\CrudGeneratorLaravel\Services\LoadRelationsService;

class BaseResourceFile extends BaseTemplateFile implements FileTemplateInterface
{
    private array $modelArray;

    public function __construct(string $name, string $namespace, string $stubFilePath, $modelArray)
    {
        $this->modelArray = $modelArray;
        parent::__construct($name, $namespace, $stubFilePath);
    }
    public function getReplacements()
    {
        return [
            '{{resourceNamespace}}' => $this->getNamespace(),
            '{{resourceName}}' => $this->getName(),
            '{{resourceRelatedFields}}' => $this->getResourceRelatedFields(),
        ];
    }

    public static function create($data): self {
        return new self($data['name'], $data['namespace'], $data['stubFilePath']);
    }

    private function getResourceRelatedFields(){
        return (new LoadRelationsService($this->modelArray))->getResult();
    }
}
