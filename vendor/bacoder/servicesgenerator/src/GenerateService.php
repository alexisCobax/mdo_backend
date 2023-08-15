<?php

namespace Bacoder\Servicesgenerator;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateService extends Command
{
    protected $signature = 'generate:service {model}';
    protected $description = 'Generate a service class and controller based on a model';

    public function handle()
    {
        $model = $this->argument('model');
        $modelName = $this->generateModelName($model);
        $className = $this->generateClassName($model);
        $serviceContent = $this->generateServiceContent($modelName, $className);
        $controllerContent = $this->generateControllerContent($modelName, $className);
        $serviceFilePath = app_path('Services/' . $className . 'Service.php');
        $controllerFilePath = app_path('Http/Controllers/' . $className . 'Controller.php');

        File::ensureDirectoryExists(app_path('Services'));
        File::put($serviceFilePath, $serviceContent);

        File::ensureDirectoryExists(app_path('Http/Controllers'));
        File::put($controllerFilePath, $controllerContent);

        $this->addRoutes($className);

        $this->info("The service class '{$className}Service', controller class '{$className}Controller', and routes have been generated successfully.");
    }

    private function generateModelName($model)
    {
        return ucfirst($model);
    }

    private function generateClassName($model)
    {
        return ucfirst($model);
    }

    private function generateServiceContent($modelName, $className)
    {
        $stubPath = base_path('vendor/bacoder/servicesgenerator/stubs/services/service.stub');
        $stubContent = File::get($stubPath);
        $replacements = [
            '{{ModelName}}' => $modelName,
            '{{ClassName}}' => $className,
            '{{modelName}}' => lcfirst($modelName),
        ];
        $content = str_replace(array_keys($replacements), array_values($replacements), $stubContent);

        return $content;
    }

    private function generateControllerContent($modelName, $className)
    {
        $stubPath = base_path('vendor/bacoder/servicesgenerator/stubs/controllers/controller.stub');
        $stubContent = File::get($stubPath);
        $replacements = [
            '{{ModelName}}' => $modelName,
            '{{ClassName}}' => $className,
            '{{modelName}}' => lcfirst($modelName),
        ];
        $content = str_replace(array_keys($replacements), array_values($replacements), $stubContent);

        return $content;
    }

    private function addRoutes($className)
    {
        $apiRoutesPath = base_path('routes/api.php');
        $routesContent = File::get($apiRoutesPath);

        $routesToAdd = [
            "",
            "/** {$className} Routes **/",
            "",
            "Route::get('/" . strtolower($className) . "', [{$className}Controller::class, 'index']);",
            "Route::get('/" . strtolower($className) . "/{id}', [{$className}Controller::class, 'show']);",
            "Route::post('/" . strtolower($className) . "', [{$className}Controller::class, 'create']);",
            "Route::put('/" . strtolower($className) . "/{id}', [{$className}Controller::class, 'update']);",
            "Route::delete('/" . strtolower($className) . "/{id}', [{$className}Controller::class, 'delete']);",
        ];

        $routesContent = str_replace("<?php\n\nuse Illuminate\Http\Request;", "<?php\n\nuse Illuminate\Http\Request;\nuse App\Http\Controllers\\{$className}Controller;", $routesContent);
        $routesContent = preg_replace('/\n\nuse Illuminate\Support\Facades\Route;\n/', "\n\nuse Illuminate\Support\Facades\Route;\n\n", $routesContent);
        $routesContent .= "\n\n" . implode("\n", $routesToAdd);

        File::put($apiRoutesPath, $routesContent);
    }
}
