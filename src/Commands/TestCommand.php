<?php

namespace Maxkamov48\CrudGeneratorLaravel\Commands;



use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Maxkamov48\CrudGeneratorLaravel\Generator\BaseControllerFile;
use Maxkamov48\CrudGeneratorLaravel\Generator\BaseModelFile;
use Maxkamov48\CrudGeneratorLaravel\Generator\BaseRequestFile;
use Maxkamov48\CrudGeneratorLaravel\Generator\BaseResourceFile;
use Maxkamov48\CrudGeneratorLaravel\Generator\Contracts\FileTemplateInterface;
use Maxkamov48\CrudGeneratorLaravel\Generator\CoreGenerator;
use Maxkamov48\CrudGeneratorLaravel\Services\ProjectService;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:all {--folder=}';

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
        $option = $this->option('folder');
        $prefix = $this->getPrefix($option);
//        dd($prefix);

        foreach ($this->projectService->getModels() as $model) {
            $modelName = $model['name'];
            $baseModel = new BaseModelFile(
                "$modelName",
                'App\Models',
                'Model'
            );
            $baseCreateRequest = new BaseRequestFile(
                $modelName . "CreateRequest",
                'App\Http\Requests'.$prefix,
                'api/RequestCustom',
                $model,
            );
            $this->checkAndWrite($service, $baseCreateRequest);

            $baseUpdateRequest = new BaseRequestFile(
                $modelName.'UpdateRequest',
                'App\Http\Requests'.$prefix,
                'api/RequestCustom',
                $model,
            );
            $this->checkAndWrite($service, $baseUpdateRequest);

            $baseResource = new BaseResourceFile(
                $modelName.'Resource',
                'App\Http\Resources'.$prefix,
                'api/ResourceCustom',
                $model
            );
            $this->checkAndWrite($service, $baseResource);

            $baseControllerFile = new BaseControllerFile(
                $modelName.'Controller',
                'App\Http\Controllers\Api'.$prefix,
                'api/ControllerCustom',
                $baseModel,
                $baseCreateRequest,
                $baseUpdateRequest,
                $baseResource
            );
            $this->checkAndWrite($service, $baseControllerFile);
            $this->info("Generation completed!");
        }



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

    private function getPrefix($prefix){
        if($prefix == null){
            return '';
        }
        return "\\$prefix";
    }
}
