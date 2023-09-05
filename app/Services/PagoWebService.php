<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Transaccion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\CarritoHelper;
use App\Models\Carritodetalle;
use App\Helpers\PaginateHelper;
use Illuminate\Validation\ValidationException;

class PagoWebService
{
    public function findAll(Request $request)
    {
        // try {
        //     $data = PaginateHelper::getPaginatedData($request, Banner::class);
        //     return response()->json(['data' => $data], Response::HTTP_OK);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        // }
    }

    public function findById(Request $request)
    {
        // $data = Banner::find($request->id);

        // return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $pago = $this->creditCard($request->amount, $request->token);

        $jsonData = $pago->getContent(); 
        $data = json_decode($jsonData); 

        $carrito = CarritoHelper::getCarrito();

        $productosCarrito = Carritodetalle::where('carrito',$carrito['id'])->get();
        
        $productosPedidos = [];

        $stock = '';

        foreach($productosCarrito as $pc){
        
        $producto = Producto::find($pc['producto']);

        if($pc['cantidad']>$producto['stock']){
            $stock = $producto['stock'];
        }else{
            $stock = $pc['cantidad'];
        }

        // $productosPedidos[] = [
        //     "producto" => $pc['producto'],
        //     "stock" => $producto['stock'],
        //     "cantidad" => $pc['cantidad'],
        //     "carrito" => $pc['carrito'],
        //     "precio" => $pc['precio'],
        //     "stock" => $stock
        // ];
        }

        $pedido = new Pedido;
        $pedido->fecha = NOW();
        $pedido->cliente = $carrito['cliente'];      
        $pedido->estado = 4;
        $pedido->vendedor = 1;
        $pedido->formaDePago = 1;
        $pedido->invoice = 0;
        $pedido->total = '0.00';
        $pedido->descuentoPorcentual = '0.00';
        $pedido->descuentoNeto = '0.00';
        $pedido->totalEnvio = '0.00';
        $pedido->origen = 1;
        $pedido->save();

        

        $transaccion = new Transaccion;
        $transaccion->fecha = NOW();
        $transaccion->cliente = $carrito['cliente'];
        $transaccion->pedido = $pedido->id;
        $transaccion->resultado = $data->status;
        $transaccion->ctr = $jsonData;
        $transaccion->save();

        dd('ok');
        dd($productosPedidos);

        $data = $request->all();
        $banner = Banner::create($data);

        if (!$banner) {
            return response()->json(['error' => 'Failed to create Banner'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($banner, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        // $banner = Banner::find($request->id);

        // if (!$banner) {
        //     return response()->json(['error' => 'Banner not found'], Response::HTTP_NOT_FOUND);
        // }

        // $banner->update($request->all());
        // $banner->refresh();

        // return response()->json($banner, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        // $banner = Banner::find($request->id);

        // if (!$banner) {
        //     return response()->json(['error' => 'Banner not found'], Response::HTTP_NOT_FOUND);
        // }

        // $banner->delete();

        // return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

    public function creditCard($amount,$token)
    {
        try {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://scl-sandbox.dev.clover.com/v1/charges');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"amount\":" . $amount . ",\"currency\":\"usd\",\"source\":\"" . $token . "\"}");

            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'Authorization: Bearer 859c0171-ee8b-7c4b-7a07-3a02288fbc03';
            $headers[] = 'idempotency-key ' . $this->gen_uuid();
            $headers[] = 'Content-Type: application/json';

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            
            $response = json_decode($response);

            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);


            return response()->json($response, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    function gen_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

}
