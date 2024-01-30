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
       
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('G')->setWidth(40);
        $sheet->getColumnDimension('H')->setWidth(40);
        $sheet->getColumnDimension('I')->setWidth(40);
        $sheet->getColumnDimension('J')->setWidth(40);
        $sheet->getColumnDimension('K')->setWidth(40);
        $sheet->getColumnDimension('L')->setWidth(40);

        // Obtener el rango de celdas
$rangoCeldas = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow());

// Establecer la alineación a la izquierda para todas las celdas
$rangoCeldas->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);


        $writer = new Xlsx($spreadsheet);
       
        $nombreArchivo = date('YmdHjs').'.xlsx';
        $rutaArchivo = storage_path('app/' . $nombreArchivo);
       
        $writer->save($rutaArchivo);
       
        return response()->download($rutaArchivo, $nombreArchivo)->deleteFileAfterSend(true);
    }
}
