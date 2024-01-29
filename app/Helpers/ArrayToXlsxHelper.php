<?php

namespace App\Helpers;

use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ArrayToXlsxHelper
{
    /**
     * getPaginatedData.
     *
     * @param  mixed $request
     * @param  mixed $model
     * @return void
     */
    public static function getXlsx($request, $model)
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
       
        $sheet->fromArray($model, null, 'A1');
       
        $writer = new Xlsx($spreadsheet);
       
        $nombreArchivo = date('YmdHjs').'.xlsx';
        $rutaArchivo = storage_path('app/' . $nombreArchivo);
       
        $writer->save($rutaArchivo);
       
        return response()->download($rutaArchivo, $nombreArchivo)->deleteFileAfterSend(true);
    }
}
