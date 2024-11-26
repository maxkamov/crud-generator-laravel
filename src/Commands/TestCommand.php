<?php

namespace Maxkamov48\CrudGeneratorLaravel\Commands;



use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Maxkamov48\CrudGeneratorLaravel\Generator\BaseControllerFile;
use Maxkamov48\CrudGeneratorLaravel\Generator\BaseModelFile;
use Maxkamov48\CrudGeneratorLaravel\Generator\BaseRequestFile;
use Maxkamov48\CrudGeneratorLaravel\Generator\BaseResourceFile;
use Maxkamov48\CrudGeneratorLaravel\Generator\Contracts\FileTemplateInterface;
use Maxkamov48\CrudGeneratorLaravel\Generator\CoreGenerator;
use Maxkamov48\CrudGeneratorLaravel\Services\LoadRelationsService;
use Maxkamov48\CrudGeneratorLaravel\Services\ProjectService;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected ProjectService $projectService;


    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new CoreGenerator(new Filesystem());
        foreach ($this->projectService->getModels() as $model) {

            $modelName = $model['name'];
            $baseModel = new BaseModelFile(
                "$modelName",
                'App\Models',
                'Model'
            );
            $baseCreateRequest = new BaseRequestFile(
                $modelName . "CreateRequest",
                'App\Http\Controllers\Requests',
                'api/RequestCustom',
                $model,
            );
            $this->checkAndWrite($service, $baseCreateRequest);

            $baseUpdateRequest = new BaseRequestFile(
                $modelName.'UpdateRequest',
                'App\Http\Controllers\Requests',
                'api/RequestCustom',
                $model,
            );
            $this->checkAndWrite($service, $baseUpdateRequest);
//            $service->execute($baseUpdateRequest);

            $baseResource = new BaseResourceFile(
                $modelName.'Resource',
                'App\Http\Controllers\Resources',
                'api/ResourceCustom',
                $model
            );
            $this->checkAndWrite($service, $baseResource);
//            $service->execute($baseResource);


            $baseControllerFile = new BaseControllerFile(
                $modelName.'Controller',
                'App\Http\Controllers\Api',
                'api/ControllerCustom',
                $baseModel,
                $baseCreateRequest,
                $baseUpdateRequest,
                $baseResource
            );
            $this->checkAndWrite($service, $baseControllerFile);
//            $service->execute($baseControllerFile);

            $this->info("Generation completed!");
//            dd($model);
        }


        /**
         *
         * if ($this->files->exists($modelPath) && $this->ask('Already exist Model. Do you want overwrite (y/n)?', 'y') == 'n') {
         * return $this;
         * }
         */



//        $data = $this->loadRelationsService->getResult();
//        print_r($data);


//        $models = ['phones'];
//
//        $bar = $this->output->createProgressBar(1);
//
//        foreach ($models as $model) {
//            $this->info("Model = $model");
//            $data = Artisan::call('make1:crud',[
//                'name' => $model,
//                'stack' => 'api'
//            ]);
//            print_r($data);
//            $bar->advance();
//        }
//        $this->newLine(1);
//        $bar->finish();



//        dd($service);
    }

    public function checkAndWrite(CoreGenerator $service, FileTemplateInterface $template){
        if(!$service->fileExists($template->_getPath())){
            $service->execute($template);
        } elseif (
            $service->fileExists($template->_getPath()) &&
            $this->ask("Already exist ".$template->getName()." Do you want overwrite (y/n)?", 'y') == 'n'
        ){
            //SKIPS
            $this->info("Skipping file ".$template->getName());
        } else {
            $this->info("Overriding file ".$template->getName());
            $service->execute($template);
        }
    }

//    public function usedTime(){
//        $service = new CoreGenerator(new Filesystem());
//
//
//        $baseModel = new BaseModelFile(
//            'SomeModel',
//            'App\Models',
//            'Model'
//        );
//        $service->execute($baseModel);
//
//        $baseCreateRequest = new BaseRequestFile(
//            'SomeCreateRequest',
//            'App\Http\Controllers\Requests',
//            'Request'
//        );
//        $service->execute($baseCreateRequest);
//
//        $baseUpdateRequest = new BaseRequestFile(
//            'SomeUpdateRequest',
//            'App\Http\Controllers\Requests',
//            'Request'
//        );
//        $service->execute($baseUpdateRequest);
//
//        $baseResource = new BaseResourceFile(
//            'SomeResource',
//            'App\Http\Controllers\Resources',
//            'api/ResourceCustom'
//        );
//        $service->execute($baseResource);
//
//        $baseControllerFile = new BaseControllerFile(
//            'SomeController',
//            'App\Http\Controllers\Api',
//            'api/ControllerCustom',
//            $baseModel,
//            $baseCreateRequest,
//            $baseUpdateRequest,
//            $baseResource
//        );
//        $service->execute($baseControllerFile);
//    }
}
