<?php

/** funcion para generar request de forma masiva si en la db el campo esta como no nulo **/

// Cargar la aplicación Laravel
require dirname(__DIR__).'/vendor/autoload.php';
$app = require_once dirname(__DIR__).'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Obtener la estructura de la tabla desde la base de datos
$tableName = 'cupondescuento'; // Reemplaza con el nombre de tu tabla
$tableColumns = Schema::getColumnListing($tableName);

// Obtener los campos no nulos de la tabla (excluyendo el campo de la clave primaria)
$nonNullableColumns = [];
foreach ($tableColumns as $column) {
    if ($column !== 'id') {
        $columnSchema = DB::selectOne("SHOW COLUMNS FROM $tableName WHERE Field = '$column'");
        if ($columnSchema->Null === 'NO') {
            $nonNullableColumns[] = $column;
        }
    }
}

// Generar las reglas de validación y mensajes
$rules = [];
$messages = [];

foreach ($nonNullableColumns as $column) {
    $rules[$column] = 'required';
    $messages[$column . '.required'] = Str::title(str_replace('_', ' ', $column)) . ' es requerido';
}

// Generar la clase CotizacionRequest
$requestClass = "<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CotizacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return " . var_export($rules, true) . ";
    }

    protected function failedValidation(Validator \$validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Error de validación',
            'data'      => \$validator->errors()
        ]));
    }

    public function messages()
    {
        return " . var_export($messages, true) . ";
    }
}";

// Guardar la clase en un archivo
$filePath = app_path('Http/Requests/'.$tableName.'Request.php');
file_put_contents($filePath, $requestClass);

echo "Clase '.$tableName.'Request generada exitosamente en: $filePath";
