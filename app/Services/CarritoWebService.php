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
use Illuminate\Support\Facades\DB;
use App\Services\CotizacionService;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnvioCotizacionMailConAdjunto;
use App\Transformers\Carrito\FindAllTransformer;
use PDOException;

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
            $this->deleteSiNoExiste($carrito['id']);
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

    public function deleteSiNoExiste($id)
    {
        try{
        $sqlCount = "
            SELECT COUNT(*) as total
            FROM carritodetalle cd
            LEFT JOIN producto p ON cd.producto = p.id
            WHERE cd.carrito = :id;
        ";

        $countResult = DB::selectOne($sqlCount, ['id' => $id]);

        if (empty($countResult) || $countResult->total == 0) return;

        // Eliminar registro inválido con SQL plano
        $sqlDelete = "
            DELETE cd
            FROM carritodetalle cd
            LEFT JOIN producto p ON cd.producto = p.id
            WHERE cd.carrito = :id
              AND (p.id IS NULL OR p.borrado IS NOT NULL)
        ";

        DB::delete($sqlDelete, ['id' => $id]);
        }catch(PDOException $e){
            return $e->getMessage();
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

        if (!$carritoHelper['id']) {
            return response()->json(['error' => 'El carro de compras no existe'], Response::HTTP_NOT_FOUND);
        }

        $carritoDetalle = DB::table('carritodetalle')
            ->select('producto', 'precio', 'cantidad')
            ->where('carrito', $carritoHelper['id'])
            ->get();

        $total = $carritoDetalle->reduce(fn($carry, $item) => $carry + ($item->precio * $item->cantidad), 0);

        // Crear cotización
        $cotizacion = new Cotizacion([
            'fecha' => now(),
            'cliente' => $carritoHelper['cliente'],
            'total' => $total,
            'estado' => 0,
            'descuento' => '0.00',
        ]);

        try {
            $cotizacion->save();
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $idCotizacion = $cotizacion->id;

        $detalles = $carritoDetalle->map(function ($cd) use ($idCotizacion) {
            return [
                'cotizacion' => $idCotizacion,
                'producto' => $cd->producto,
                'precio' => $cd->precio,
                'cantidad' => isset($cd->cantidad) ? $cd->cantidad : 0
            ];
        })->toArray();

        Cotizaciondetalle::insert($detalles);

        // Actualizar el estado del carrito directamente
        DB::table('carrito')
            ->where('id', $carritoHelper['id'])
            ->update(['estado' => 1]);


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
