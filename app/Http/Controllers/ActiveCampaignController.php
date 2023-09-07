<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ActiveCampaignController extends Controller
{

    public function subirCuenta(Request $request)
    {

        $objCliente = $request->input('cliente');

        $resultadoFuncion = "";
        $respuesta = "";

        // if ($objCliente['IdActiveCampaign'] != 0) {
        //     return "";
        // }

        $objCliente['Email'] = 'test@example.com';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cobax1694091376.api-us1.com/api/3/ecomCustomers?filters[email]=test%40example.com',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Api-Token: c7efcf1f232e6e487613cf97ff21e13d2b02db40a6c2fc88dcf349d0c9da59b833a6053c',
                'Cookie: PHPSESSID=df45b062daf77f2f705cff843c3f6531; cmp226508783=bda45332837f3e7d1741fb81c284196b; em_acp_globalauth_cookie=6cb85ff2-8a98-4418-a5e8-9dd7f1d611d8'
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        }
        curl_close($curl);

        dd($response);

        $respuesta = $cliente->body();
        $resultadoFuncion = $respuesta;
        $resultado = json_decode($respuesta);

        if (count($resultado->ecomCustomers) > 0) {
            $objCliente['IdActiveCampaign'] = $resultado->ecomCustomers[0]->id;
            // Guardar el cliente en Laravel, probablemente usando Eloquent
            // $objDatosCliente->guardar($objCliente);

            if ($resultado->ecomCustomers[0]->externalid != $objCliente['Id']) {
                // Realizar la modificación en Laravel
                $this->modificacion('ecomCustomers/' . $objCliente['IdActiveCampaign'], [
                    'ecomCustomer' => [
                        'externalid' => $objCliente['Id']
                    ]
                ]);
            }
        } else {
            $postData = [
                'ecomCustomer' => [
                    'connectionid' => '2',
                    'externalid' => $objCliente['Id'],
                    'email' => $objCliente['Email'],
                    'acceptsMarketing' => '1'
                ]
            ];

            $respuesta = $this->alta('ecomCustomers/', $postData);
            $resultadoFuncion = $respuesta;

            try {
                $resultado = json_decode($respuesta);
                $objCliente['IdActiveCampaign'] = $resultado->ecomCustomer->id;
                // Guardar el cliente en Laravel, probablemente usando Eloquent
                // $objDatosCliente->guardar($objCliente);
            } catch (Exception $ex) {
                $resultadoFuncion .= $ex->getMessage();
            }
        }

        // Realizar la modificación en Laravel
        $resultadoFuncion .= "Modificacion: " . $this->modificacion('ecomCustomers/' . $objCliente['IdActiveCampaign'], [
            'ecomCustomer' => [
                'externalid' => $objCliente['Id']
            ]
        ]);

        return $resultadoFuncion;
    }

    public function SubirContacto($objCliente)
    {
        $resultadoFuncion = "";
        // Implementa la lógica de SubirContacto aquí
        return $resultadoFuncion;
    }

    public function NuevaEtiqueta($objCliente, $idEtiqueta)
    {
        $resultadoFuncion = "";
        // Implementa la lógica de NuevaEtiqueta aquí
        return $resultadoFuncion;
    }

    public function SubirPedido($objInvoice)
    {
        $resp = "";
        // Implementa la lógica de SubirPedido aquí
        return $resp;
    }

    public function SubirCotizacion($objCotizacion)
    {
        $resp = "";
        // Implementa la lógica de SubirCotizacion aquí
        return $resp;
    }

    public function Alta($strUrl, $strMensaje)
    {
        // Implementa la lógica de Alta aquí
    }

    public function Consulta($strUrl, $strMensaje)
    {
        // Implementa la lógica de Consulta aquí
    }

    public function Modificacion($strUrl, $strMensaje)
    {
        // Implementa la lógica de Modificacion aquí
    }

    public function ConsultarCampos()
    {
        // Implementa la lógica de ConsultarCampos aquí
    }
}
