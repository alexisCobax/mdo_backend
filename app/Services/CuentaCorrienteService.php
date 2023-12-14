<?php

namespace App\Services;

use App\Models\Recibo;
use App\Models\Cliente;
use App\Models\Invoice;
use App\Models\Reintegro;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CuentaCorrienteService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findById(Request $request)
    {

        $clienteId = $request->id;
        $fechaInicio = '2015-09-01';
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        $cliente = Cliente::where('id',$clienteId)->first();

        $resultados = Recibo::select('id', 'cliente', DB::raw("'RECIBO' AS comprobante"), 'id AS numero', 'fecha', 'total', DB::raw("CONCAT('/recibo/', id, '.pdf') AS link"))
            ->where('anulado', 0)
            ->where('fecha', '>=', $fechaInicio)
            ->where('cliente', $clienteId)
            ->union(
                Reintegro::select('id', 'cliente', DB::raw("'REINTEGRO' AS comprobante"), DB::raw("'id' AS numero"), 'fecha', DB::raw('(total * -1) AS total'), DB::raw("CONCAT('/reintegro/', id, '.pdf') AS link"))
                    ->where('anulado', 0)
                    ->where('fecha', '>=', $fechaInicio)
                    ->where('cliente', $clienteId)
            )
            ->union(
                Invoice::select('id', 'cliente', DB::raw("'INVOICE' AS comprobante"), 'id AS numero', 'fecha', DB::raw('(total * -1) AS total'), DB::raw("CONCAT('/invoice/', id, '.pdf') AS link"))
                    ->where('anulada', 0)
                    ->where('fecha', '>=', $fechaInicio)
                    ->where('cliente', $clienteId)
            )
            ->orderBy('cliente', 'asc')
            ->orderBy('numero', 'asc')
            ->orderBy('comprobante', 'desc')
            //->toSql();
            //echo $resultados;die;
            ->paginate($perPage, ['*'], 'page', $page);

        $total = Recibo::where('anulado', 0)
            ->where('fecha', '>=', $fechaInicio)
            ->where('cliente', $clienteId)
            ->sum('total');

        $total += Reintegro::where('anulado', 0)
            ->where('fecha', '>=', $fechaInicio)
            ->where('cliente', $clienteId)
            ->sum(DB::raw('ABS(total)'));

        $total += Invoice::where('anulada', 0)
            ->where('fecha', '>=', $fechaInicio)
            ->where('cliente', $clienteId)
            ->sum(DB::raw('ABS(total)'));

        $paginatedData = [
            'headers' => [],
            'original' => [
                'status' => 200,
                'total' => $resultados->total(),
                'cantidad_por_pagina' => $resultados->perPage(),
                'pagina' => $resultados->currentPage(),
                'cantidad_total' => $resultados->total(),
                'results' => $resultados->items(),
                'exception' => null,
                'cliente' => $cliente->nombre,
                'ctacteTotal' => $total
            ],
        ];

        return response()->json($paginatedData, Response::HTTP_OK);

    }

    public function create(Request $request)
    {
        //--
    }

    public function update(Request $request)
    {
        //--
    }

    public function delete(Request $request)
    {
        //--
    }
}
