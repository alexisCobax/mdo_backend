<?php

namespace App\Helpers;

use App\Models\ConfiguracionesGenerales;
use Illuminate\Http\Response;

class GoHighLevelHelper
{
    public static function get($cantidad, $producto)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://services.leadconnectorhq.com/oauth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'client_id=66b679c804185f34bdb417c0-lzn5i47i&client_secret=a7e15a6f-989f-45ba-b642-3bd6beaa0a9e&grant_type=refresh_token&code=&refresh_token=eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdXRoQ2xhc3MiOiJMb2NhdGlvbiIsImF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJzb3VyY2UiOiJJTlRFR1JBVElPTiIsInNvdXJjZUlkIjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwLWx6bjVpNDdpIiwiY2hhbm5lbCI6Ik9BVVRIIiwicHJpbWFyeUF1dGhDbGFzc0lkIjoiNDBVZWNMVTdkWjRLZExlcEo3VVIiLCJvYXV0aE1ldGEiOnsic2NvcGVzIjpbImNvbnRhY3RzLnJlYWRvbmx5IiwiY29udGFjdHMud3JpdGUiXSwiY2xpZW50IjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwIiwiY2xpZW50S2V5IjoiNjZiNjc5YzgwNDE4NWYzNGJkYjQxN2MwLWx6bjVpNDdpIn0sImlhdCI6MTcyMzcyMDE1Ny43OTYsImV4cCI6MTc1NTI1NjE1Ny43OTYsInVuaXF1ZUlkIjoiMGMxY2RkMDMtN2YzMy00NTg5LWFlOTUtOTU3ZjY1ZjNlNTY1In0.FvZNX6AJY3mr0jfUTJ7mO5FoFRPPqScIjf98TO09_GI0gDJ7ey9n4oKoAzLxZ_oQFhDPNJT6Q5JsoxYcUS4Oa_-ksebbk7xXhfXfBx2iZuQmkbYD-cGRYJjQANFX9VclXHMCPeD5xDAQ6PsyVNoPhb63PMNg2TBk92bdF3DOmPUQtRvOP7rWEWBvOYei5R4j3oxueGPOdwqoGyBIROciNy4oXSyzNYDykZgxGq_E49HmSt61OiH6kSyUCOtRHhPDF2fVLtcvbSnGccylxmWmhsKa4F0EHxLjkD-_dPOg207be-xiqChiVb9wUb7EqXj_7IGCgFDbfHVywxLMkc7Q8HSThUPJiQUSsUu_-8Viy_n3BAP3WRlJw5XYmhwhzyuGh-xSCSHUtvuaJkz4FSWDutQQEF8HXZWyF_AiLu9KhuxUjznfakW4A5hQe6r1InXf4pp2kDK8hp7wQjQkVICDiXwszDohSzT8l2pmjNY0FBCIkNeVKVRHJpZpYpAOaThitg7htdZVo4KfTo6C_-_9Of0DgRmgkqXQTTK3pkeMnQQf1wW3icHP8UIUgKeN0aZOgoF_2EmVag0SFDrhhXi98JQyURIxFVvAC73Oj3t-ntpJblpWxJhtcyzL-6_6Ak-KE7LbI8UWGFtRheYNQbgUIJfD6Ocm4VHZEl8PJbzXDDE&user_type=Location&redirect_uri=',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;die;


        $array = json_decode($response, true);

        echo $array['refresh_token'];

        $token = ConfiguracionesGenerales::where('nombre', 'token')->first();

        $refresh = ConfiguracionesGenerales::where('nombre', 'refresh')->first();

        if ($cantidad > $producto->stock) {
            return response()->json(['mensaje' => 'No hay stock suficiente', 'cantidad' => $producto->stock, 'status' => false], Response::HTTP_OK);
        } else {
            return response()->json(['mensaje' => '', 'cantidad' => $cantidad, 'status' => true], Response::HTTP_OK);
        }
    }
}
