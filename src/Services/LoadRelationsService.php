<?php

namespace Maxkamov48\CrudGeneratorLaravel\Services;


class LoadRelationsService
{
//    protected ProjectService $projectService;

    protected $singleData;

    /**
     * @param ProjectService $projectService
     */
    public function __construct($singleData)
    {
//        array:6 [
//        "class_object" => "App\Models\Access"
//  "name" => "Access"
//  "table" => "accesses"
//  "fillable" => array:4 [
//        0 => "user_id"
//    1 => "role_id"
//    2 => "merchant_id"
//    3 => "name"
//  ]
//  "casts" => array:5 [
//        "id" => "int"
//    "user_id" => "int"
//    "role_id" => "int"
//    "merchant_id" => "int"
//    "deleted_at" => "datetime"
//  ]
//  "relations" => array:3 [
//        "merchant" => array:2 [
//        "type" => "BelongsTo"
//      "related" => "App\Models\Merchant"
//    ]
//    "role" => array:2 [
//        "type" => "BelongsTo"
//      "related" => "App\Models\Role"
//    ]
//    "cashRegisters" => array:2 [
//        "type" => "BelongsToMany"
//      "related" => "App\Models\CashRegister"
//    ]
//  ]
//]
        $this->singleData = $singleData;
    }


    public function getResult(){
        $singleData = $this->singleData;
        $singleProperties = $singleData;
        $totalTextId = "'id'". ' => ' . "\$this->id";
        return implode(", \n", [
            ...[$totalTextId],
            ...$this->getColumnFields($singleProperties['fillable']),
            ...$this->getRelationsFields($singleProperties['relations']),
        ]);
    }

    private function getRelationsFields($relations){
        return array_map(function($relation, $key){
            $type = $relation['type'];
            $fieldName = $key;
            $functionName = $this->camelToSnakeCase($fieldName);
//            $classNameResource = $className."Resource";

            $temp = $relation['related'];
            $arr = explode("\\",$temp);
            $className = $arr[count($arr) - 1];
            $classNameResource = $className."Resource";


            if($this->isSingle($type)){
                return $this->getSingleLoad($classNameResource, $fieldName, $functionName);
            }else{
                return $this->getManyLoad($classNameResource, $fieldName, $functionName);
            }
        }, $relations, array_keys($relations));
    }

    private function getColumnFields($fillables){
        return array_map(function ($item){
            $totalText = "'$item'". ' => ' . "\$this->$item";
            return $totalText;
        }, $fillables);
    }
    private function camelToSnakeCase($string){
        // Regular expression to match uppercase letters
        $pattern = '/([A-Z])/u';
        // Replacement string with an underscore before the matched letter
        $replacement = '_$1';
        // Perform the replacement and convert the entire string to lowercase
        return strtolower(preg_replace($pattern, $replacement, $string));
    }

//    private function camelToPascalCase($str) {
//        // Convert camelCase to words separated by spaces
//        $words = preg_split('/(?=[A-Z])/', $str);
//
//        // Capitalize the first letter of each word
//        $pascalCaseStr = ucwords(implode(' ', $words));
//
//        // Remove spaces
//        return str_replace(' ', '', $pascalCaseStr);
//    }

    private function getManyLoad($classNameResource, $fieldName, $functionName){
        $text = <<<EOT
'$functionName' => $classNameResource::collection(\$this->whenLoaded('$fieldName'))
EOT;
        return $text;
    }

    private function getSingleLoad($classNameResource, $fieldName, $functionName){
        $text = <<<EOT
'$functionName' => new $classNameResource(\$this->whenLoaded('$fieldName'))
EOT;
        return $text;
    }

    private function isSingle($type){
        return $type === 'BelongsTo' || $type === 'HasOne' || $type === 'BelongsToMany';
    }
}
