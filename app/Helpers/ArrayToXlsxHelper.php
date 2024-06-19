<?php

namespace App\Helpers;

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

     public static function getXlsx($model, $cabeceras, $totalColumns = [])
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

         $highestRow = $sheet->getHighestRow();

         if (!empty($totalColumns)) {
             $currentRow = $highestRow + 1;

             // Añadir línea negra de separación
             $sheet->getStyle('A' . $currentRow . ':' . $sheet->getHighestColumn() . $currentRow)
                   ->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK)
                   ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));

             foreach ($totalColumns as $totalColumn) {
                 $total = array_sum(array_column($model, $totalColumn['column']));

                 // Agregar la fila de total
                 $sheet->setCellValue('H' . ($currentRow + 1), $totalColumn['label']);
                 $sheet->setCellValue('I' . ($currentRow + 1), $total);

                 // Poner en negrita la celda del total
                 $sheet->getStyle('H' . ($currentRow + 1))->getFont()->setBold(true);

                 $currentRow++;
             }
         }

         $rangoCeldas = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow());
         $rangoCeldas->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

         $writer = new Xlsx($spreadsheet);

         $nombreArchivo = date('YmdHjs') . '.xlsx';
         $rutaArchivo = storage_path('app/' . $nombreArchivo);

         $writer->save($rutaArchivo);

         return response()->download($rutaArchivo, $nombreArchivo)->deleteFileAfterSend(true);
     }



    // public static function getXlsx($model, $cabeceras)
    // {

    //     $spreadsheet = new Spreadsheet();

    //     $sheet = $spreadsheet->getActiveSheet();
    //     if ($cabeceras) {
    //         $sheet->fromArray([$cabeceras], null, 'A1');
    //         $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
    //     }
    //     foreach (range('A', 'L') as $columna) {
    //         $sheet->getColumnDimension($columna)->setWidth(40);
    //     }

    //     $sheet->fromArray($model, null, 'A2');

    //     $sheet->getColumnDimension('B')->setWidth(40);
    //     $sheet->getColumnDimension('C')->setWidth(40);
    //     $sheet->getColumnDimension('D')->setWidth(40);
    //     $sheet->getColumnDimension('E')->setWidth(40);
    //     $sheet->getColumnDimension('F')->setWidth(40);
    //     $sheet->getColumnDimension('G')->setWidth(40);
    //     $sheet->getColumnDimension('H')->setWidth(40);
    //     $sheet->getColumnDimension('I')->setWidth(40);
    //     $sheet->getColumnDimension('J')->setWidth(40);
    //     $sheet->getColumnDimension('K')->setWidth(40);
    //     $sheet->getColumnDimension('L')->setWidth(40);

    //     $rangoCeldas = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow());

    //     $rangoCeldas->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

    //     $writer = new Xlsx($spreadsheet);

    //     $nombreArchivo = date('YmdHjs') . '.xlsx';
    //     $rutaArchivo = storage_path('app/' . $nombreArchivo);

    //     $writer->save($rutaArchivo);

    //     return response()->download($rutaArchivo, $nombreArchivo)->deleteFileAfterSend(true);
    // }
}
