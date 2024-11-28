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
            '{{docBlock}}' => $this->getDocBlock(),
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

    private function getDocBlock(){
        $result = '';
        foreach ($this->modelArray['fillable'] as $value) {
            $result .= '* @bodyParam '. $value . "\n \t";
        }

        $docBlock = <<<EOT
/**
     * Get the validation rules that apply to the request.
     $result
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
EOT;
        return $docBlock;
    }
}
