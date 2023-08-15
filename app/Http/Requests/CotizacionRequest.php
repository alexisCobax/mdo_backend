<?php

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
        return [

            'total' => 'required',
            'cliente' => 'required',
            'fecha' => 'required',
        ];
    }



    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Error de validacion',
            'data'      => $validator->errors()
        ]));
    }



    public function messages()
    {

        return [
            'total.required' => 'Total es requerido',
            'cliente.required' => 'Body es requerido',
            'fecha.required' => 'Fecha es requerido'

        ];
    }
}
