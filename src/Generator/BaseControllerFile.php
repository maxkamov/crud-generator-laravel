<?php

namespace Maxkamov48\CrudGeneratorLaravel\Generator;

use Illuminate\Support\Str;
use Maxkamov48\CrudGeneratorLaravel\Generator\Contracts\FileTemplateInterface;

class BaseControllerFile extends BaseTemplateFile implements FileTemplateInterface
{

    private BaseModelFile $baseModel;

    private BaseRequestFile $baseCreateRequest;

    private BaseRequestFile $baseUpdateRequest;

    private BaseResourceFile $baseResource;

    /**
     * @param BaseModelFile $baseModel
     */
    public function __construct($name, $namespace, $stubFile,
                                BaseModelFile $baseModel,
                                BaseRequestFile $baseCreateRequest,
                                BaseRequestFile $baseUpdateRequest,
                                BaseResourceFile $baseResource
    )
    {
        $this->baseModel = $baseModel;
        $this->baseCreateRequest = $baseCreateRequest;
        $this->baseUpdateRequest = $baseUpdateRequest;
        $this->baseResource = $baseResource;
        parent::__construct($name,$namespace,$stubFile);
    }


    public function getReplacements()
    {
        return [
            '{{controllerNamespace}}' => 'App\Http\Controllers',

            '{{apiControllerNamespace}}' => $this->getNamespace(),
            '{{controllerName}}' => $this->getName(),
            '{{modelNamespace}}' => $this->baseModel->getNamespace(),
            '{{modelName}}' => $this->baseModel->getName(),

            '{{modelNamePluralLowerCase}}' => Str::camel(Str::plural($this->baseModel->getName())),
            '{{modelNamePluralUpperCase}}' => ucfirst(Str::plural($this->baseModel->getName())),
            '{{modelNameLowerCase}}' => Str::camel($this->baseModel->getName()),

            '{{createRequestName}}' => $this->baseCreateRequest->getName(),
            '{{createRequestNamespace}}' => $this->baseCreateRequest->getNamespace(),

            '{{updateRequestName}}' => $this->baseUpdateRequest->getName(),
            '{{updateRequestNamespace}}' => $this->baseUpdateRequest->getNamespace(),

            '{{resourceNamespace}}' => $this->baseResource->getNamespace(),
            '{{resourceName}}' => $this->baseResource->getName(),
        ];
    }

    public static function create($data): self {
        return new self($data['name'], $data['namespace'], $data['stubFilePath']);
    }
}
