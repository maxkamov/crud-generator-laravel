<?php

namespace Maxkamov48\CrudGeneratorLaravel\Generator;

use Maxkamov48\CrudGeneratorLaravel\Generator\Contracts\FileTemplateInterface;

class BaseRequestFile extends BaseTemplateFile implements FileTemplateInterface
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
            '{{requestNamespace}}' => $this->getNamespace(),
            '{{modelName}}' => $this->getName(),
            '{{rules}}' => $this->getRules(),
        ];
    }

    public static function create($data): self {
        return new self($data['name'], $data['namespace'], $data['stubFilePath']);
    }

    private function getRules(){
        return join(', '.PHP_EOL, $this->getColumnFields($this->modelArray['casts'], $this->modelArray['fillable']));
    }

    private function getColumnFields($casts, $fillables){
        $result = [];
        foreach($fillables as $key => $fillable){
//            $data = "'$key' => '$cast'";
            $data = "'$fillable' => 'sometimes'";
            array_push($result, $data);
        }
        return $result;
    }
}
