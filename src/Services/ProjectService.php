<?php

namespace Maxkamov48\CrudGeneratorLaravel\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Facades\Route;


class ProjectService
{
    public function getModel(string $modelClass)
    {
        $namespace = $modelClass;
        if (!class_exists($namespace)) {
            throw new \DomainException('Unable to load model');
        }
        $modelInstance = new $namespace();
        return [
            'class_object' => $namespace,
            'name' => class_basename($modelInstance),
            'table' => $modelInstance->getTable(),
            'fillable' => $modelInstance->getFillable(),
            'casts' => $modelInstance->getCasts(),
            'relations' => self::getAllRelations($namespace),
        ];
    }

    public function getModels()
    {

        $modelsPath = app_path('Models');

        $files = File::allFiles($modelsPath);

        $modelsData = [];

        foreach ($files as $file) {

            $namespace = 'App\\Models\\' . Str::replaceLast('.php', '', $file->getFilename());
            if (class_exists($namespace)) {
                $modelInstance = new $namespace();

                $modelsData[] = [
                    'class_object' => $namespace,
                    'name' => class_basename($modelInstance),
                    'table' => $modelInstance->getTable(),
                    'fillable' => $modelInstance->getFillable(),
                    'casts' => $modelInstance->getCasts(),
                    'relations' => self::getAllRelations($namespace),
                ];
            }
        }

        return $modelsData;
    }

    public static function getAllRelations(string $modelClass)
    {
        $model = new $modelClass();
        $reflection = new ReflectionClass($model);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        $relations = [];

        foreach ($methods as $method) {
            // Skip methods that are not defined in the given model class
            if ($method->class != $reflection->getName()) {
                continue;
            }

            // Skip methods with parameters
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            try {
                // Attempt to get the return value of the method
                $returnValue = $method->invoke($model);

                // Check if the return value is an instance of a Relation
                if ($returnValue instanceof Relation) {
                    $relations[$method->name] = [
                        'type' => class_basename($returnValue),
                        'related' => get_class($returnValue->getRelated())
                    ];
                }
            } catch (\Throwable $e) {
                // Catch and ignore any exceptions or errors
                continue;
            }
        }

        return $relations;
    }


    public function getTableColumns()
    {
        $tables = DB::select("SELECT tablename
        FROM pg_tables
        WHERE schemaname = 'public'");

        //sort by name
        usort($tables, function ($a, $b) {
            return strcmp($a->tablename, $b->tablename);
        });

        //add execption for tables
        $exceptTables = [
            'telescope_entries',
            'telescope_entries_tags',
            'telescope_monitoring',
            'jobs',
            'failed_jobs',
            'migrations',
            'password_resets',
            'sessions',
            'password_reset_tokens',
            'oauth_access_tokens',
            'oauth_auth_codes',
            'oauth_clients',
            'oauth_personal_access_clients',
            'oauth_refresh_tokens',
            'oauth_scopes',
            'oauth_users',
            'personal_access_tokens',
            'cache',
            'cache_locks',
            'tests',
            'test_user',
        ];

        $tables = array_filter($tables, function ($table) use ($exceptTables) {
            return !in_array($table->tablename, $exceptTables);
        });

        foreach ($tables as $table) {

            $tableName = $table->tablename; // Извлекаем имя таблицы

            $data[$tableName]['data'] = DB::table($tableName)
                ->limit(3)
                ->get()
                ->toArray();

            $data[$tableName]['columns'] = (array) DB::select("SELECT column_name
            FROM information_schema.columns
            WHERE table_name = '$tableName'");
        }

        return $data;
    }

    public function getRoutes()
    {
        $routes = Route::getRoutes();

        $data = [];
        foreach ($routes as $route) {
            if (!in_array('api', $route->middleware())) {
                continue;
            }

            $data[] = [
                'path' => $route->uri,
                'methods' => $route->methods,
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $route->middleware(),
                'prefix' => $route->getPrefix(),
            ];
        }
        return $data;
    }
}
