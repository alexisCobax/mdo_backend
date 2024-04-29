<?php

namespace App\Filters\Reportes\Stock;

use App\Helpers\DateHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StockFilters
{
    public static function getPaginateStock($request)
    {

            // Inicializa la consulta SQL
            $query = DB::table('producto')
                ->select(
                    'id AS idProducto',
                    'codigo',
                    'nombre AS nombreProducto',
                    'color',
                    'stock',
                    'costo',
                    'precio',
                    DB::raw('stock * costo AS CostoStock'),
                    DB::raw('stock * precio AS PrecioStock')
                )
                ->where('stock', '>', 0);
            $stock = $query->get();

            // Devuelve la respuesta
            return response()->json([
                'status' => Response::HTTP_OK,
                'results' => $stock,
            ]);
    }
}
