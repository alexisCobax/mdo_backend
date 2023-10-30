<?php

namespace App\Helpers;

use Illuminate\Http\Response;

class JsonToCsvHelper
{
    /**
     * getPaginatedData.
     *
     * @param  mixed $request
     * @param  mixed $model
     * @return void
     */
    public static function getCsv($request, $model)
    {
        $data = array(
            array('Nombre', 'Email', 'Teléfono'),
            array('Juan Pérez', 'juan@example.com', '123-456-7890'),
            array('María López', 'maria@example.com', '987-654-3210'),
        );

        $filename = 'productos.csv';

        $output = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        fclose($output);

        exit();
    }
}
