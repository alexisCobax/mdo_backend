<?php

namespace App\Services;

use App\Models\Carrito;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\PaginateHelper;
use Illuminate\Support\Facades\DB;
use App\Transformers\Carrito\FindAllTransformer;
use Illuminate\Support\Facades\Auth;

class CarritoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Carrito::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findAllVendedor(Request $request)
    {

        try {
            $page    = max(1, (int) $request->input('pagina', env('PAGE', 1)));
            $perPage = max(1, (int) $request->input('cantidad', env('PER_PAGE', 15)));
            $offset  = ($page - 1) * $perPage;
        
            DB::statement("SET lc_time_names = 'es_ES'");
        
            // Filtros dinámicos
            $filters = [];
            $params = [];
        
            if ($request->filled('nombre')) {
                $filters[] = "cliente.nombre LIKE ?";
                $params[] = '%' . $request->input('nombre') . '%';
            }
        
            if ($request->filled('fecha')) {
                $filters[] = "DATE(carrito.fecha) = ?";
                $params[] = $request->input('fecha'); // ejemplo: 2025-07-01
            }
        
            if ($request->filled('telefono')) {
                $filters[] = "cliente.telefono = ?";
                $params[] = $request->input('telefono');
            }
        
            if ($request->filled('email')) {
                $filters[] = "cliente.email = ?";
                $params[] = $request->input('email');
            }
        
            if ($request->filled('whatsapp')) {
                $filters[] = "cliente.whatsapp = ?";
                $params[] = $request->input('whatsapp');
            }
        
            // WHERE final
            //$where = "carrito.estado = 0";
            $where = 1;
            if (!empty($filters)) {
                $where .= " AND " . implode(" AND ", $filters);
            }

            $user = Auth::user();
        
            // Consulta principal con filtros
            $SQL1 = "SELECT 
                        carrito.id, 
                        carrito.gestionadopor, 
                        CASE
                            WHEN carrito.gestionadopor IS NULL OR carrito.gestionadopor = 0 THEN 'Libre'
                            ELSE usuario.nombre
                        END AS gestionado_nombre,
                        DATE_FORMAT(
                            IF(carrito.fecha_modificacion IS NOT NULL, carrito.fecha_modificacion, carrito.fecha),
                            '%d-%b-%Y'
                        ) AS fecha, 
                        cliente.nombre, 
                        cliente.telefono, 
                        cliente.email, 
                        cliente.whatsapp,
                        (SELECT SUM(carritodetalle.cantidad) FROM carritodetalle WHERE carritodetalle.carrito = carrito.id) AS total_productos
                    FROM carrito
                    LEFT JOIN cliente ON carrito.cliente = cliente.id
                    LEFT JOIN usuario ON carrito.gestionadopor = usuario.id
                    WHERE $where
                    ORDER BY carrito.fecha_modificacion DESC
                    LIMIT ? OFFSET ?;
                    ";
        
            // Parámetros para la consulta con paginación
            $paramsWithPagination = array_merge($params, [$perPage, $offset]);
            $rows = DB::select($SQL1, $paramsWithPagination);
        
            // Consulta para el total con los mismos filtros
            $SQL2 = "SELECT COUNT(*) AS total
                     FROM carrito
                     LEFT JOIN cliente ON carrito.cliente = cliente.id
                     WHERE $where";
        
            $totalObj = DB::selectOne($SQL2, $params);
            $total = (int) $totalObj->total;
        
            return response()->json([
                'status'              => Response::HTTP_OK,
                'total'               => $total,
                'cantidad_por_pagina' => $perPage,
                'pagina'              => $page,
                'cantidad_total'      => $total,
                'results'             => $rows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error al obtener los carrito',
                'detalle' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    public function findById(Request $request)
    {
        $data = Carrito::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
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
                //'cupon' => 18, //este cupon fuerza a un 15% de decuento para toda la tienda es temporal
                'fecha_modificacion' => NOW()
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
}
