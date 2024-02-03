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
    public static function getXlsx($model, $cabeceras)
    {

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        if ($cabeceras) {
            $sheet->fromArray([$cabeceras], null, 'A1');
            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        }
        foreach (range('A', 'L') as $columna) {
            $sheet->getColumnDimension($columna)->setWidth(40);
        }

        $sheet->fromArray($model, null, 'A2');

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

        $rangoCeldas = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow());

        $rangoCeldas->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);


        $writer = new Xlsx($spreadsheet);

        $nombreArchivo = date('YmdHjs') . '.xlsx';
        $rutaArchivo = storage_path('app/' . $nombreArchivo);

        $writer->save($rutaArchivo);

        return response()->download($rutaArchivo, $nombreArchivo)->deleteFileAfterSend(true);
    }
}
