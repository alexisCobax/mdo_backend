<?php

namespace App\Services;

class ActiveCampaignService
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

    public function SubirContacto($request)
    {

        // $payload =  [
        //     "contact" => [
        //         "email" => "johndoe6@example.com",
        //         "firstName" => "John",
        //         "lastName" => "Doe",
        //         "phone" => "7223224241",
        //         "fieldValues" => [
        //             [
        //                 "field" => "17",
        //                 "value" => "9"
        //             ]
        //         ]
        //     ]
        // ];

        $payload = [
            'contact' => [
                'email' => $request->email,
                'firstName' => $request->nombre,
                'lastName' => $request->apellido,
                'phone' => $request->telefono,
                'fieldValues' => [
                    [
                        'field' => '17',
                        'value' => '9',
                    ],
                ],
            ],
        ];

        $postData = json_encode($payload);

        $response = $this->post('https://cobax1694091376.api-us1.com/api/3/contacts', $postData);
        $response = json_decode($response, true);

        return $response['contact']['id'];
    }
}
