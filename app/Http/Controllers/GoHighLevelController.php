<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionesGenerales;

class GoHighLevelController extends Controller
{

    public function post($url, $payload)
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Api-Token: 9904cef61c8e26e3464150e2885a455c302e300e8a5026008822b814237abc6bf77426d7',
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function getRefreshToken(){



        $configuraciones = ConfiguracionesGenerales::whereIn('valor', ['ghl_client_id', 'ghl_client_secret'])->pluck('valor', 'campo');

$ghlClientId = $configuraciones->get('ghl_client_id');
$ghlClientSecret = $configuraciones->get('ghl_client_secret');

echo $ghlClientSecret;die;



        // Inicializa cURL
$curl = curl_init();

// Configura las opciones de cURL
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => http_build_query(array(
        'client_id' => '66b679c804185f34bdb417c0-lzn5i47i',
        'client_secret' => 'a7e15a6f-989f-45ba-b642-3bd6beaa0a9e',
        'grant_type' => 'refresh_token',
        'code' => '',
        'refresh_token' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdXRoQ2xhc3MiOiJMb2NhdGlvbiIsImF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJzb3VyY2UiOiJJTlRFR1JBVElPTiIsInNvdXJjZUlkIjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwLWx6bjVpNDdpIiwiY2hhbm5lbCI6Ik9BVVRIIiwicHJpbWFyeUF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJvYXV0aE1ldGEiOnsic2NvcGVzIjpbImNvbnRhY3RzLnJlYWRvbmx5IiwiY29udGFjdHMud3JpdGUiXSwiY2xpZW50IjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwIiwiY2xpZW50S2V5IjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwLWx6bjVpNDdpIn0sImlhdCI6MTcyMzcyMDE1Ny43OTYsImV4cCI6MTc1NTI1NjE1Ny43OTYsInVuaXF1ZUlkIjoiMGMxY2RkMDMtN2YzMy00NTg5LWFlOTUtOTU3ZjY1ZjNlNTY1In0.FvZNX6AJY3mr0jfUTJ7mO5FoFRPPqScIjf98TO09_GI0gDJ7ey9n4oKoAzLxZ_oQFhDPNJT6Q5JsoxYcUS4Oa_-ksebbk7xXhfXfBx2iZuQmkbYD-cGRYJjQANFX9VclXHMCPeD5xDAQ6PsyVNoPhb63PMNg2TBk92bdF3DOmPUQtRvOP7rWEWBvOYei5R4j3oxueGPOdwqoGyBIROciNy4oXSyzNYDykZgxGq_E49HmSt61OiH6kSyUCOtRHhPDF2fVLtcvbSnGccylxmWmhsKa4F0EHxLjkD-_dPOg207be-xiqChiVb9wUb7EqXj_7IGCgFDbfHVywxLMkc7Q8HSThUPJiQUSsUu_-8Viy_n3BAP3WRlJw5XYmhwhzyuGh-xSCSHUtvuaJkz4FSWDutQQEF8HXZWyF_AiLu9KhuxUjznfakW4A5hQe6r1InXf4pp2kDK8hp7wQjQkVICDiXwszDohSzT8l2pmjNY0FBCIkNeVKVRHJpZpYpAOaThitg7htdZVo4KfTo6C_-_9Of0DgRmgkqXQTTK3pkeMnQQf1wW3icHP8UIUgKeN0aZOgoF_2EmVag0SFDrhhXi98JQyURIxFVvAC73Oj3t-ntpJblpWxJhtcyzL-6_6Ak-KE7LbI8UWGFtRheYNQbgUIJfD6Ocm4VHZEl8PJbzXDDE',
        'user_type' => 'Location',
        'redirect_uri' => ''
    )),
    CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Content-Type' => 'application/x-www-form-urlencoded'
    ),
));

// Ejecuta la solicitud y obtiene la respuesta
$response = curl_exec($curl);

// Cierra la sesión cURL
curl_close($curl);

// Verifica si la respuesta es válida y decodifica el JSON
if ($response === false) {
    echo 'Error en la solicitud cURL.';
    die();
}

$array = json_decode($response, true);

if (json_last_error() === JSON_ERROR_NONE) {
    // Imprime el valor del refresh token
    echo $array['refresh_token'];
} else {
    echo 'Error al decodificar la respuesta JSON.';
}


    // $curl = curl_init();

    // curl_setopt_array($curl, array(
    // CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
    // CURLOPT_RETURNTRANSFER => true,
    // CURLOPT_ENCODING => '',
    // CURLOPT_MAXREDIRS => 10,
    // CURLOPT_TIMEOUT => 0,
    // CURLOPT_FOLLOWLOCATION => true,
    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    // CURLOPT_CUSTOMREQUEST => 'POST',
    // CURLOPT_POSTFIELDS => 'client_id=66b679c804185f34bdb417c0-lzn5i47i&client_secret=a7e15a6f-989f-45ba-b642-3bd6beaa0a9e&grant_type=refresh_token&code=&refresh_token=eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdXRoQ2xhc3MiOiJMb2NhdGlvbiIsImF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJzb3VyY2UiOiJJTlRFR1JBVElPTiIsInNvdXJjZUlkIjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwLWx6bjVpNDdpIiwiY2hhbm5lbCI6Ik9BVVRIIiwicHJpbWFyeUF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJvYXV0aE1ldGEiOnsic2NvcGVzIjpbImNvbnRhY3RzLnJlYWRvbmx5IiwiY29udGFjdHMud3JpdGUiXSwiY2xpZW50IjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwIiwiY2xpZW50S2V5IjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwLWx6bjVpNDdpIn0sImlhdCI6MTcyMzcyMDE1Ny43OTYsImV4cCI6MTc1NTI1NjE1Ny43OTYsInVuaXF1ZUlkIjoiMGMxY2RkMDMtN2YzMy00NTg5LWFlOTUtOTU3ZjY1ZjNlNTY1In0.FvZNX6AJY3mr0jfUTJ7mO5FoFRPPqScIjf98TO09_GI0gDJ7ey9n4oKoAzLxZ_oQFhDPNJT6Q5JsoxYcUS4Oa_-ksebbk7xXhfXfBx2iZuQmkbYD-cGRYJjQANFX9VclXHMCPeD5xDAQ6PsyVNoPhb63PMNg2TBk92bdF3DOmPUQtRvOP7rWEWBvOYei5R4j3oxueGPOdwqoGyBIROciNy4oXSyzNYDykZgxGq_E49HmSt61OiH6kSyUCOtRHhPDF2fVLtcvbSnGccylxmWmhsKa4F0EHxLjkD-_dPOg207be-xiqChiVb9wUb7EqXj_7IGCgFDbfHVywxLMkc7Q8HSThUPJiQUSsUu_-8Viy_n3BAP3WRlJw5XYmhwhzyuGh-xSCSHUtvuaJkz4FSWDutQQEF8HXZWyF_AiLu9KhuxUjznfakW4A5hQe6r1InXf4pp2kDK8hp7wQjQkVICDiXwszDohSzT8l2pmjNY0FBCIkNeVKVRHJpZpYpAOaThitg7htdZVo4KfTo6C_-_9Of0DgRmgkqXQTTK3pkeMnQQf1wW3icHP8UIUgKeN0aZOgoF_2EmVag0SFDrhhXi98JQyURIxFVvAC73Oj3t-ntpJblpWxJhtcyzL-6_6Ak-KE7LbI8UWGFtRheYNQbgUIJfD6Ocm4VHZEl8PJbzXDDE&user_type=Location&redirect_uri=',
    // CURLOPT_HTTPHEADER => array(
    //     'Accept: application/json',
    //     'Content-Type: application/x-www-form-urlencoded'
    // ),
    // ));

    // $response = curl_exec($curl);

    // curl_close($curl);
    // echo $response;die;


    // $array = json_decode($response, true);

    // echo $array['refresh_token'];

    }

    public function SubirContacto($request)
    {

        $payload = [
            "contact" => [
                "id" => "i7W5cngJ6F6l8np0K8Z6",
                "dateAdded" => "2024-08-15T11:12:55.071Z",
                "dateUpdated" => "2024-08-15T11:12:55.071Z",
                "deleted" => false,
                "tags" => [
                    "nisi sint commodo amet",
                    "consequat"
                ],
                "type" => "lead",
                "customFields" => [],
                "locationId" => "40UecLU7dZ4KdLepJ7UR",
                "firstName" => "juan",
                "firstNameLowerCase" => "juan",
                "fullNameLowerCase" => "juan fuentes",
                "lastName" => "fuentes",
                "lastNameLowerCase" => "fuentes",
                "email" => "juan@fuentes.com",
                "emailLowerCase" => "juan@fuentes.com",
                "bounceEmail" => false,
                "unsubscribeEmail" => false,
                "phone" => "+18888888888",
                "address1" => "3535 1st St N",
                "city" => "Dolomite",
                "state" => "AL",
                "country" => "US",
                "postalCode" => "35061",
                "website" => "https://www.tesla.com",
                "source" => "public api",
                "companyName" => "DGS VolMAX",
                "timezone" => "America/Chihuahua",
                "dnd" => true,
                "dndDate" => "2024-08-15T11:12:55.072Z",
                "dndSettings" => [
                    "Call" => [
                        "status" => "active",
                        "message" => "string",
                        "code" => "string"
                    ],
                    "Email" => [
                        "status" => "active",
                        "message" => "string",
                        "code" => "string"
                    ],
                    "SMS" => [
                        "status" => "active",
                        "message" => "string",
                        "code" => "string"
                    ],
                    "WhatsApp" => [
                        "status" => "active",
                        "message" => "string",
                        "code" => "string"
                    ],
                    "GMB" => [
                        "status" => "active",
                        "message" => "string",
                        "code" => "string"
                    ],
                    "FB" => [
                        "status" => "active",
                        "message" => "string",
                        "code" => "string"
                    ]
                ],
                "inboundDndSettings" => [
                    "all" => [
                        "status" => "active",
                        "message" => "string"
                    ]
                ],
                "gender" => "male",
                "createdBy" => [
                    "source" => "INTEGRATION",
                    "channel" => "OAUTH",
                    "sourceId" => "66b679c804185f34bdb417c0-lzn5i47i",
                    "timestamp" => "2024-08-15T11:12:55.071Z"
                ],
                "lastUpdatedBy" => [
                    "source" => "INTEGRATION",
                    "channel" => "OAUTH",
                    "sourceId" => "66b679c804185f34bdb417c0-lzn5i47i",
                    "timestamp" => "2024-08-15T11:12:55.071Z"
                ],
                "lastSessionActivityAt" => "2024-08-15T11:12:55.160Z",
                "validEmail" => null,
                "validEmailDate" => null
            ],
            "traceId" => "494a7862-4dc9-4165-a5b2-fed3443636aa"
        ];


        $postData = json_encode($payload);

        $response = $this->post('https://cobax1694091376.api-us1.com/api/3/contacts', $postData);
        $response = json_decode($response, true);

        return $response['contact']['id'];
    }

    public function filtrarContact($request){



    }
}
