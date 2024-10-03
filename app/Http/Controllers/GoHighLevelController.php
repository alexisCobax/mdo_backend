<?php

namespace App\Http\Controllers;

use App\Services\GoHighLevelService;


class GoHighLevelController extends Controller
{
    public static function createContact()
    {
        /**
         * payload de ejemplo
         **/
        $payload = [
            "firstName" => "ale5",
            "lastName" => "alexis5",
            "name" => "COBAX5 PRUEBAS5",
            "email" => "ale5@alex5.com",
            "gender" => "male",
            "phone" => "+1 348-182-8888",
            "address1" => "3535 1st St N",
            "city" => "Dolomite",
            "state" => "AL",
            "postalCode" => "35061",
            "website" => "https://www.tesla.com",
            "timezone" => "America/Chihuahua",
            "dnd" => true,
            "source" => "public api",
            "country" => "US",
            "companyName" => "DGS VolMAX",
            "tags" => ["masterlist"]
        ];

        $response = GoHighLevelService::createContact($payload);

        if (isset($response['error']) && $response['error'] === true) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Contacto creado exitosamente.',
            'data' => $response
        ], 200);
    }
}
