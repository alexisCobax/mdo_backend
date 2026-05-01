<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Cotizaciondetalle;
use App\Models\Cotizacion;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class CotizaciondetalleService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Cotizaciondetalle::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {

        $cotizacion = Cotizacion::where('id', $request->id)->first();
        
        $cliente = Cliente::where('id', $cotizacion->cliente)->first();
    
        $clienteDatos = [
            "nombre"    => $cliente->nombre ?? '',
            "email"     => $cliente->email ?? '',
            "telefono"  => $cliente->telefono ?? '',
            "domicilio" => $cliente->domicilio ?? '',
            "whatsapp"  => $cliente->whatsapp ?? ''
        ];
    

        $baseUrl = env('URL_IMAGENES_PRODUCTOS');

        $SQL = "SELECT 
        cotizaciondetalle.precio,
        cotizaciondetalle.cantidad,
        producto.nombre AS producto,
        marcaproducto.nombre AS nombremarca,
        IF(fotoproducto.url IS NOT NULL, 
            fotoproducto.url, 
            CONCAT(?, producto.imagenPrincipal, '.jpg')
        ) AS imagen,
        (cotizaciondetalle.precio * cotizaciondetalle.cantidad) AS total
        FROM 
        cotizaciondetalle
        LEFT JOIN 
        producto ON cotizaciondetalle.producto=producto.id
        LEFT JOIN 
        fotoproducto ON fotoproducto.id = producto.imagenPrincipal
        LEFT JOIN marcaproducto ON marcaproducto.id = producto.marca
        WHERE 
        cotizacion = ?";

        $data = DB::select($SQL, [$baseUrl,$request->id]);


        $response = [
            'status' => Response::HTTP_OK,
            // 'total' => $data->total(),
            // 'cantidad_por_pagina' => $data->perPage(),
            // 'pagina' => $data->currentPage(),
            // 'cantidad_total' => $data->total(),
            'results' => $data,
            'cliente' => $clienteDatos,

        ];

        return response()->json($response);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $cotizaciondetalle = Cotizaciondetalle::create($data);

        if (!$cotizaciondetalle) {
            return response()->json(['error' => 'Failed to create Cotizaciondetalle'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($cotizaciondetalle, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $cotizaciondetalle = Cotizaciondetalle::find($request->id);

        if (!$cotizaciondetalle) {
            return response()->json(['error' => 'Cotizaciondetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $cotizaciondetalle->update($request->all());
        $cotizaciondetalle->refresh();

        return response()->json($cotizaciondetalle, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $cotizaciondetalle = Cotizaciondetalle::find($request->id);

        if (!$cotizaciondetalle) {
            return response()->json(['error' => 'Cotizaciondetalle not found'], Response::HTTP_NOT_FOUND);
        }

        $cotizaciondetalle->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
