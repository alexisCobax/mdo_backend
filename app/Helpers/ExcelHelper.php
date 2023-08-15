<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class ExcelHelper
{
    public static function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->getClientOriginalExtension() === 'xlsx' || $file->getClientOriginalExtension() === 'xls') {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('compras/excel', $fileName, 'public');

                return response()->json(['message' => 'Archivo subido exitosamente', 'path' => $filePath]);
            } else {
                return response()->json(['error' => 'El archivo debe ser un archivo de Excel (xlsx o xls)'], 400);
            }
        }

        return response()->json(['message' => 'No se ha proporcionado ningún archivo'], 400);

    }

}
