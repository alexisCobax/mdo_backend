<?php

namespace App\Services;

use Error;
use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\CarritoHelper;
use App\Models\Carritodetalle;
use App\Models\Cotizaciondetalle;
use App\Services\CotizacionService;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnvioCotizacionMailConAdjunto;
use App\Transformers\Carrito\FindAllTransformer;

class CarritoWebService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findByToken(Request $request)
    {
        $carrito = CarritoHelper::getCarrito();

        if ($carrito['id']) {
            return $this->findCarritoDetalle($carrito['id']);
        } else {
            $data = [
                'fecha' => NOW(),
                'cliente' => $carrito['cliente'],
                'estado' => 0,
                'vendedor' => 1,
                'formaPago' => 1,
            ];

            $carrito = Carrito::create($data);

            return ['data' => ['carrito' => $carrito['id']]];
        }
    }

    public function findStatus(Request $request)
    {
        $carrito = Carrito::where('cliente', $request->id)
            ->where('estado', 0)
            ->first();

        if ($carrito) {
            return $this->findCarritoDetalle($carrito->id);
        } else {
            $data = [
                'fecha' => NOW(),
                'cliente' => $request->id,
                'estado' => 0,
                'vendedor' => 1,
                'formaPago' => 1,
            ];

            $carrito = Carrito::create($data);

            return ['data' => ['carrito' => $carrito->id]];
        }
    }

    public function findCarritoDetalle($id)
    {
        $transformer = new FindAllTransformer();
        if ($transformer) {
            return response()->json(['data' => $transformer->transform($id)], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'No se encontraron datos'], Response::HTTP_NOT_FOUND);
        }
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $carrito = Carrito::create($data);

        if (!$carrito) {
            return response()->json(['error' => 'Failed to create Carrito'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($carrito, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $carrito = Carrito::find($request->id);

        if (!$carrito) {
            return response()->json(['error' => 'Carrito not found'], Response::HTTP_NOT_FOUND);
        }

        $carrito->update($request->all());
        $carrito->refresh();

        return response()->json($carrito, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $carrito = Carrito::find($request->id);

        if (!$carrito) {
            return response()->json(['error' => 'Carrito not found'], Response::HTTP_NOT_FOUND);
        }

        $carrito->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

    public function procesar()
    {

        $carritoHelper = CarritoHelper::getCarrito();

        //Cotizacion::where('cliente', $carritoHelper['cliente'])->update(['estado' => 1]);

        if (!$carritoHelper['id']) {
            return response()->json(['error' => 'El carro de compras no existe'], Response::HTTP_NOT_FOUND);
        }

        $carritoDetalle = Carritodetalle::where('carrito', $carritoHelper['id'])->get();

        $total = $carritoDetalle->map(function ($detalle) {
            return $detalle->precio * $detalle->cantidad;
        })->sum();

        $carrito = Carrito::find($carritoHelper['id'])->first();

        $cotizacion = new Cotizacion;
        $cotizacion->fecha = NOW();
        $cotizacion->cliente = $carritoHelper['cliente'];
        $cotizacion->total = $total;
        $cotizacion->estado = 0;
        $cotizacion->descuento = '0.00';
        try {
            $cotizacion->save();
        } catch (Error $e) {
            echo $e->getMessage();
            die;
        }

        $idCotizacion = $cotizacion->id;

        foreach ($carritoDetalle as $cd) {

            $cotizacionDetalle = new Cotizaciondetalle;
            $cotizacionDetalle->cotizacion = $idCotizacion;
            $cotizacionDetalle->producto = $cd['producto'];
            $cotizacionDetalle->precio = $cd['precio'];
            $cotizacionDetalle->cantidad = isset($cd['cantidad']) ? $cd['cantidad'] : 0;
            $cotizacionDetalle->save();
        }

        $carritoUpdate = Carrito::find($carritoHelper['id']);
        $carritoUpdate->estado = 1;
        $carritoUpdate->save();

        if (!$cotizacion) {
            return response()->json(['error' => 'Cotizacion not found'], Response::HTTP_NOT_FOUND);
        }

        // ACA MANDAR EL EMAIL

        // $cotizacion = new CotizacionService();

        // $cotizacion->generarCotizacionMailPdf($idCotizacion);

        // $cliente = Cliente::where('id', $carritoHelper['cliente'])->first();

        // /** Envio por email PDF**/
        // $cuerpo = '';
        // $emailMdo = env('MAIL_COTIZACION_MDO');
        // if ($cliente->email) {

        //     $destinatarios = [
        //         $emailMdo,
        //         $cliente->email,
        //     ];
        // } else {
        //     $destinatarios = [
        //         $emailMdo,
        //     ];
        // }

        // $rutaArchivoZip = storage_path('app/public/tmpdf/' . 'cotizacion_' . $idCotizacion . '.pdf');

        // $rutaArchivoFijo = storage_path('app/public/fijos/Inf.TRANSFERENCIA_BANCARIA.pdf');

        // Mail::to($destinatarios)->send(new EnvioCotizacionMailConAdjunto($cuerpo, $rutaArchivoZip, $rutaArchivoFijo));

        return response()->json(['data' => $cotizacion], Response::HTTP_OK);
    }
}
