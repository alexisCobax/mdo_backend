<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class GenerateDocumentation extends Command
{
    protected $signature = 'generate:documentation';

    protected $description = 'Generates API documentation';

    public function handle()
    {

        $this->generate();
    }

    public function generate()
    {

        $routesAll = Route::getRoutes()->getRoutes();
        $prefix = 'api';

        $routes = array_filter($routesAll, function ($route) use ($prefix) {
            $routePrefix = $route->getPrefix();
            return str_starts_with($routePrefix, $prefix);
        });

        // Crear una colección de Postman
        $collection = [
            'info' => [
                'name' => 'API MDO Documentation',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            ],
            'item' => []
        ];

        foreach ($routes as $route) {
            // Obtener información de la ruta
            $routeMethods = $route->methods();
            $routeUri = $route->uri();
            $routeAction = $route->getAction();
            $Name = str_replace($prefix . '/', '', $route->uri());
            $routeName = $routeMethods[0] . ' ' . ucfirst($Name);

            // Crear un nuevo elemento en la colección de Postman para la ruta actual
            $item = [
                'name' => $routeName ?: 'Untitled Request',
                'request' => [
                    'url' => "{{base_url}}/$routeUri",
                    'method' => $routeMethods[0],
                    'header' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ]
                ]
            ];

            // Obtener el nombre de modelo a partir del routeUri
            $modelName = $this->getModelNameFromRouteUri($routeUri);

            // Agregar los bodyParams utilizando la función getFillableAttributes
            if ($routeMethods[0] === 'POST' || $routeMethods[0] === 'PUT') {
                $bodyParams = $this->getFillableAttributes($modelName);
                if ($bodyParams !== null) {
                    $item['request']['body'] = [
                        'mode' => 'raw',
                        'raw' => $bodyParams
                    ];
                }
            }

            $collection['item'][] = $item;
        }

        // Obtener el contenido JSON de la colección
        $jsonContent = json_encode($collection, JSON_PRETTY_PRINT);

        // Guardar el archivo en la carpeta 'postman' dentro de 'storage'
        Storage::disk('local')->put('postman/postman_collection.json', $jsonContent);

        echo '¡Coleccion generada!';
    }

    public function getModelNameFromRouteUri($routeUri)
    {

        $modelName = explode('/', $routeUri);
        $modelName = ucfirst($modelName[1]);

        return ucfirst($modelName);
    }

    public function getFillableAttributes($modelName)
    {

        // Obtener el nombre completo de la clase del modelo
        $modelClass = "App\\Models\\" . $modelName;
        // Verificar si la clase del modelo existe
        if (!class_exists($modelClass)) {
            return null;
        }

        // Crear una instancia del modelo
        $modelInstance = new $modelClass();

        // Obtener los atributos fillable del modelo
        $fillableAttributes = $modelInstance->getFillable();

        // Eliminar el atributo "id" de los atributos fillable, si existe
        $fillableAttributes = array_diff($fillableAttributes, ['id']);

        // Crear un array asociativo para los atributos fillable
        $fillableArray = [];
        foreach ($fillableAttributes as $attribute) {
            $fillableArray[$attribute] = '';
        }

        // Retornar los atributos fillable en formato JSON
        return json_encode($fillableArray, JSON_PRETTY_PRINT);
    }
}
