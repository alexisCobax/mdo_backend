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
    public static function getPaginatedData($request, $model)
    {
        $jsonData = $request->json()->all();

        if (empty($jsonData)) {
            return response()->json(['error' => 'El JSON está vacío'], Response::HTTP_BAD_REQUEST);
        }

        $csvData = '';
        $headers = [];

        foreach ($jsonData as $row) {
            if (empty($headers)) {
                $headers = array_keys($row);
                $csvData .= implode(',', $headers) . "\n";
            }

            $csvData .= implode(',', $row) . "\n";
        }

        $response = Response::make($csvData, Response::HTTP_OK);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-Disposition', 'attachment; filename="data.csv"');

        return $response;
    }
}
