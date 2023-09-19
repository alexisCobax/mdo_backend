<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\ActiveCampaignService;
use Exception;

class ActiveCampaignController extends Controller
{
    private $ApiToken = 'c7efcf1f232e6e487613cf97ff21e13d2b02db40a6c2fc88dcf349d0c9da59b833a6053c';
    private $url = 'https://cobax1694091376.api-us1.com/api/3/';

    private $service;

    public function __construct(ActiveCampaignService $ActiveCampaignService)
    {
        $this->service = $ActiveCampaignService;
    }

    // Subir un ciente al eCommerce - esto lo ejecuto cuando el cliente cambia de prospecto a cliente (endpoint generar nuevo cliente)
    public function SubirCuenta() // objeto cliente con todos sus datos
    {
        $objCliente = Cliente::find(1);

        $resultadoFuncion = '';
        $respuesta = '';
        $resultado = '';

        // if ($objCliente->IdActiveCampaign !== 0) {
        //     return "";
        // }

        $objDatosCliente = new Cliente();

        //si no esta en la db local el active campaign
        if ($objCliente->IdActiveCampaign === 0) {
            $respuesta = $this->Consulta('ecomCustomers?filters[email]=' . $objCliente->Email);

            dd($respuesta);
            $resultadoFuncion = $respuesta;
            $resultado = json_decode($respuesta);
            dd($resultado);
            if (count($resultado->ecomCustomers) > 0) {
                //grabo el id 4 en la db esto corresponde al cliente primero, el dato lo guardo en activecampaign
                $objCliente->IdActiveCampaign = $resultado->ecomCustomers[0]->id;
                $objDatosCliente->guardar($objCliente); //update

                if ($resultado->ecomCustomers[0]->externalid !== $objCliente->Id) { // si el external id no es igual al id de la db lo update
                    $this->Modificacion('ecomCustomers/' . $objCliente->IdActiveCampaign, '{"ecomCustomer":{"externalid":"' . $objCliente->Id . '"}}');
                }
            } else {// sino lo doy de alta
                $postData = '{"ecomCustomer": {"connectionid": "2", "externalid": "' . $objCliente->Id . '", "email": "' . $objCliente->Email . '", "acceptsMarketing": "1"}}';
                $respuesta = $this->Alta('ecomCustomers/', $postData);
                $resultadoFuncion = $respuesta;

                try {
                    $resultado = json_decode($respuesta);
                    $objCliente->IdActiveCampaign = $resultado->ecomCustomer->id;
                    $objDatosCliente->guardar($objCliente);
                } catch (Exception $ex) {
                    $resultadoFuncion .= $ex->getMessage();
                }
            }
        }

        return $resultadoFuncion;
    }

    // este genera el contacto FUERA DEL ECOMMERCE - esto se ejecuta cuando alguien se registra como prospecto
    public function SubirContacto($objCliente)
    {
        $resultadoFuncion = '';
        $resultado = '';
        $objDatosCliente = new Cliente();

        $postData = '{"contact": {"email": "' . $objCliente->Email . '", "firstName": "' . $objCliente->Contacto . '", "lastName": "' . $objCliente->ContactoApellido . '", "phone": "' . $objCliente->Telefono . '", "fieldValues": [{"field": "17", "value": "' . $objCliente->Pais . '"}]}}';
        $respuesta = '';

        try {
            if ($objCliente->IdActiveCampaignContact === 0) {
                $respuesta = $this->Consulta('contacts?email=' . $objCliente->Email);
                $resultado = json_decode($respuesta);

                if (count($resultado->contacts) > 0) {
                    $objCliente->IdActiveCampaignContact = $resultado->contacts[0]->id;
                    $respuesta = $this->Modificacion('contacts/' . $objCliente->IdActiveCampaign . '/', $postData);
                } else {
                    $respuesta = $this->Alta('contacts/', $postData);
                    $resultado = json_decode($respuesta);
                    $objCliente->IdActiveCampaignContact = $resultado->contacts[0]->id;
                }
            } else {
                $respuesta = $this->Modificacion('contacts/' . $objCliente->IdActiveCampaign . '/', $postData);
                $objCliente->IdActiveCampaignContact = $objCliente->IdActiveCampaign;
            }
        } catch (Exception $ex) {
        }

        $objDatosCliente->guardar($objCliente);
        $resultadoFuncion = $this->NuevaEtiqueta($objCliente, 61); //prospecto

        return $resultadoFuncion;
    }

    //genero nueva etiqueta prospecto
    public function NuevaEtiqueta($objCliente, $idEtiqueta)
    {
        $resultadoFuncion = '';

        if ($objCliente->IdActiveCampaignContact === 0) {
            return 'ERROR etiqueta sin id';
        }

        $objDatosCliente = new Cliente();
        $postData = '{"contactTag": {"contact": "' . $objCliente->IdActiveCampaignContact . '", "tag": "' . $idEtiqueta . '"}}';
        $respuesta = '';

        try {
            $respuesta = $this->Alta('contactTags/', $postData);
            $resultadoFuncion = 'etiqueta OK';
        } catch (Exception $ex) {
            $resultadoFuncion = 'etiqueta ERROR' . $postData;
        }

        return $resultadoFuncion;
    }

    //cuando genero el invoice ejecuto este funcion
    public function SubirPedido($objInvoice)
    {
        $resp = '';

        if ($objInvoice->IdActiveCampaign !== 0) {
            return 'cargado con anterioridad';
        }

        $postData = '';

        try {
            $cn = new mysqli();
            $cm = new mysqli_stmt();
            $SQL = '';
            $objClientes = new Cliente();
            $objDetalleInvoice = new InvoiceDetalle();
            $objDatosInvoice = new invoice();
            $objDatosPedidoDetalle = new pedidoDetalle();
            $objDatosPedidoDetalleNN = new pedidoDetalleNN();
            $objClientes->buscarPorId($objInvoice->oCliente);

            if ($objInvoice->oCliente->Email === 'doralice.gonzalez@gmail.com' || $objInvoice->oCliente->Email === 'clientes@mayoristasdeopticas.com') {
                $objInvoice->oCliente->IdActiveCampaign = -1;
                $objClientes->guardar($objInvoice->oCliente);

                $cn->connect();
                $cm->init($cn);
                $SQL = 'UPDATE invoice SET  IdActiveCampaign=?,  WHERE id=?';
                $cm->prepare($SQL);
                $cm->bind_param('ii', $objInvoice->Id, -1);
                $cm->execute();

                return 'Email Incorrecto';
                exit();
            }

            if ($objInvoice->oCliente->IdActiveCampaign === 0) {
                $this->SubirCuenta($objInvoice->oCliente);
            }

            $postData = '{ "ecomOrder": { "externalid": "' . $objInvoice->Id . '", "source": "1", "email": "' . $objInvoice->oCliente->Email . '","orderProducts": [ ';

            foreach ($objDatosPedidoDetalle->listarDetalleDS($objInvoice->oPedido)->tables[0]->rows as $dr) {
                $postData .= '{';
                $postData .= '"externalid": "' . $dr->item('idProducto') . '",';
                $postData .= '"name": "' . str_replace(['"', "\t", "\x14"], '', $dr->item('nombreProducto')) . '",';
                $postData .= '"price": ' . intval($dr->item('precio') * 100) . ',';
                $postData .= '"quantity": ' . $dr->item('cantidad') . ',';
                $postData .= '"category": "' . $dr->item('nombreCategoria') . '",';
                $postData .= '"sku": "' . $dr->item('codigo') . '",';
                $postData .= '"description": "' . str_replace(['"', "\t", "\x14"], '', $dr->item('descripcion')) . '",';
                $postData .= '"imageUrl": "https://mayoristasdeopticas.net/productos/' . $dr->item('imagenPrincipal') . '.jpg",';
                $postData .= '"productUrl": "https://mayoristasdeopticas.net/producto.aspx?id=' . $dr->item('idProducto') . '"';
                $postData .= '},';
            }

            foreach ($objDatosPedidoDetalleNN->listarDetalleDS($objInvoice->oPedido)->tables[0]->rows as $dr) {
                $postData .= '{';
                $postData .= '"externalid": "0",';
                $postData .= '"name": "' . str_replace(['"', "\t", "\x14"], '', $dr->item('descripcion')) . '",';
                $postData .= '"price": ' . intval($dr->item('precio') * 100) . ',';
                $postData .= '"quantity": ' . $dr->item('cantidad') . ',';
                $postData .= '"category": "0",';
                $postData .= '"sku": "",';
                $postData .= '"description": "' . str_replace(['"', "\t", "\x14"], '', $dr->item('descripcion')) . '",';
                $postData .= '"imageUrl": "https://mayoristasdeopticas.net/productos/0.jpg",';
                $postData .= '"productUrl": "https://mayoristasdeopticas.net/producto.aspx?id=0"';
                $postData .= '},';
            }

            $postData = substr($postData, 0, strlen($postData) - 1);
            $postData .= ' ],';
            $postData .= '"orderDiscounts": [';
            $postData .= '{';
            $postData .= '"name": "DescuentoTotal",';
            $postData .= '"type": "order",';
            $postData .= '"discountAmount": ' . intval(($objInvoice->SubTotal - $objInvoice->total) * 100);
            $postData .= '}';
            $postData .= '],';

            $postData .= '"orderUrl": "https://mayoristasdeopticas.net/invoice/' . $objInvoice->Id . '.pdf",';
            $postData .= '"externalCreatedDate": "' . $objInvoice->Fecha->format('c') . '",';
            $postData .= '"externalUpdatedDate": "' . $objInvoice->Fecha->format('c') . '",';
            $postData .= '"shippingMethod": "' . $objInvoice->ShipVia . '",';
            $postData .= '"totalPrice": ' . intval($objInvoice->total * 100) . ',';
            $postData .= '"shippingAmount": ' . intval($objInvoice->TotalEnvio * 100) . ',';
            $postData .= '"taxAmount": 0,';
            $postData .= '"discountAmount": ' . intval(($objInvoice->SubTotal - $objInvoice->total) * 100) . ',';
            $postData .= '"currency": "USD",';
            $postData .= '"orderNumber": "' . $objInvoice->Id . '",';
            $postData .= '"connectionid": "2",';
            $postData .= '"customerid": "' . $objInvoice->oCliente->IdActiveCampaign . '"}}';
            $respuesta = $this->Alta('ecomOrders/', $postData);
            $resp = 'RESPUESTA:' . $respuesta;
            $resultado = json_decode($respuesta);
            $objInvoice->IdActiveCampaign = $resultado->ecomOrder->id;

            $cn = new mysqli();
            $cn->connect();
            $cm = new mysqli_stmt();
            $SQL = 'UPDATE invoice SET  IdActiveCampaign=?,  WHERE id=?';
            $cm->init($cn);
            $cm->prepare($SQL);
            $cm->bind_param('ii', $objInvoice->Id, $objInvoice->IdActiveCampaign);
            $cm->execute();
        } catch (Exception $ex) {
            $resp .= ' error:' . $ex->getMessage() . ' MENSAJE ENVIADO: ' . $postData;
        }

        return $resp;
    }

    //esto se ejecuta cuanmdo genero cotizacion - endpoint carrito a cotizacion
    public function SubirCotizacion($objCotizacion)
    {
        $resp = '';

        if ($objCotizacion->IdActiveCampaign !== 0) {
            return 'cargado con anterioridad';
        }

        try {
            $cn = new mysqli();
            $cm = new mysqli_stmt();
            $SQL = '';
            $objClientes = new Cliente();
            $objDatosInvoice = new invoice();
            $objCotizacionDetalle = new cotizacionDetalle();
            $objClientes->buscarPorId($objCotizacion->oCliente);

            if ($objCotizacion->oCliente->Email === 'doralice.gonzalez@gmail.com' || $objCotizacion->oCliente->Email === 'clientes@mayoristasdeopticas.com') {
                $objCotizacion->oCliente->IdActiveCampaign = -1;
                $objClientes->guardar($objCotizacion->oCliente);

                $cn->connect();
                $cm->init($cn);
                $SQL = 'UPDATE cotizacion SET  IdActiveCampaign=?,  WHERE id=?';
                $cm->prepare($SQL);
                $cm->bind_param('ii', $objCotizacion->Id, -1);
                $cm->execute();

                return 'Email Incorrecto';
                exit();
            }

            if ($objCotizacion->oCliente->IdActiveCampaign === 0) {
                $this->SubirCuenta($objCotizacion->oCliente);
            }

            $postData = '{ "ecomOrder": { "externalcheckoutid": "' . $objCotizacion->Id . '", "source": "1", "email": "' . $objCotizacion->oCliente->Email . '","orderProducts": [ ';

            foreach ($objCotizacionDetalle->listarDetalleDS($objCotizacion)->tables[0]->rows as $dr) {
                $postData .= '{';
                $postData .= '"externalid": "' . $dr->item('idProducto') . '",';
                $postData .= '"name": "' . str_replace(['"', "\t", "\x14"], '', $dr->item('nombreProducto')) . '",';
                $postData .= '"price": ' . intval($dr->item('precio') * 100) . ',';
                $postData .= '"quantity": ' . $dr->item('cantidad') . ',';
                $postData .= '"category": "' . $dr->item('nombreCategoria') . '",';
                $postData .= '"sku": "' . $dr->item('codigo') . '",';
                $postData .= '"description": "' . str_replace(['"', "\t", "\x14"], '', $dr->item('descripcion')) . '",';
                $postData .= '"imageUrl": "https://mayoristasdeopticas.net/productos/' . $dr->item('imagenPrincipal') . '.jpg",';
                $postData .= '"productUrl": "https://mayoristasdeopticas.net/producto.aspx?id=' . $dr->item('idProducto') . '"';
                $postData .= '},';
            }

            $postData = substr($postData, 0, strlen($postData) - 1);
            $postData .= ' ],';
            $postData .= '"orderDiscounts": [';
            $postData .= '{';
            $postData .= '"name": "DescuentoTotal",';
            $postData .= '"type": "order",';
            $postData .= '"discountAmount": 0';
            $postData .= '}';
            $postData .= '],';

            $postData .= '"orderUrl": "https://mayoristasdeopticas.net/IntranetDetalleCotizacion.aspx?id=' . $objCotizacion->Id . '",';
            $postData .= '"externalCreatedDate": "' . $objCotizacion->Fecha->format('c') . '",';
            $postData .= '"externalUpdatedDate": "' . $objCotizacion->Fecha->format('c') . '",';
            $postData .= '"abandoned_date": "' . $objCotizacion->Fecha->format('c') . '",';
            $postData .= '"shippingMethod": "",';
            $postData .= '"totalPrice": ' . intval($objCotizacion->Total * 100) . ',';
            $postData .= '"shippingAmount": 0,';
            $postData .= '"taxAmount": 0,';
            $postData .= '"discountAmount": 0,';
            $postData .= '"currency": "USD",';
            $postData .= '"orderNumber": "' . $objCotizacion->Id . '",';
            $postData .= '"connectionid": "2",';
            $postData .= '"customerid": "' . $objCotizacion->oCliente->IdActiveCampaign . '"}}';

            $respuesta = $this->Alta('ecomOrders/', $postData);
            $resultado = json_decode($respuesta);
            $objCotizacion->IdActiveCampaign = $resultado->ecomOrder->id;

            $cn->connect();
            $cm->init($cn);
            $SQL = 'UPDATE cotizacion SET  IdActiveCampaign=?,  WHERE id=?';
            $cm->prepare($SQL);
            $cm->bind_param('ii', $objCotizacion->Id, $objCotizacion->IdActiveCampaign);
            $cm->execute();
        } catch (Exception $ex) {
            $resp .= $ex->getMessage();
        }

        return $resp;
    }

    // este funcion es el helper de curl
    public function Alta($strUrl, $strMensaje)
    {
        try {

            // Create cURL resource
            $curl = curl_init();

            // Set cURL options in an array
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->url . $strUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $strMensaje,
                CURLOPT_HTTPHEADER => [
                    'Api-Token: ' . $this->ApiToken,
                    'Cookie: PHPSESSID=df45b062daf77f2f705cff843c3f6531; cmp226508783=bda45332837f3e7d1741fb81c284196b; em_acp_globalauth_cookie=6cb85ff2-8a98-4418-a5e8-9dd7f1d611d8',
                ],
            ]);

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                echo 'Error:' . curl_error($curl);
            }
            curl_close($curl);

            return $response;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    // este funcion es el helper de curl
    public function Consulta($strUrl)
    {
        try {

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->url . $strUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Api-Token: ' . $this->ApiToken,
                    'Cookie: PHPSESSID=df45b062daf77f2f705cff843c3f6531; cmp226508783=bda45332837f3e7d1741fb81c284196b; em_acp_globalauth_cookie=6cb85ff2-8a98-4418-a5e8-9dd7f1d611d8',
                ],
            ]);

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                echo 'Error:' . curl_error($curl);
            }
            curl_close($curl);

            return $response;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    // este funcion es el helper de curl
    public function Modificacion($strUrl, $strMensaje)
    {
        try {

            // Create cURL resource
            $curl = curl_init();

            // Set cURL options in an array
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->url . $strUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $strMensaje,
                CURLOPT_HTTPHEADER => [
                    'Api-Token: ' . $this->ApiToken,
                    'Cookie: PHPSESSID=df45b062daf77f2f705cff843c3f6531; cmp226508783=bda45332837f3e7d1741fb81c284196b; em_acp_globalauth_cookie=6cb85ff2-8a98-4418-a5e8-9dd7f1d611d8',
                ],
            ]);

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                echo 'Error:' . curl_error($curl);
            }
            curl_close($curl);

            return $response;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}
