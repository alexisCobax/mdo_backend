<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CuentaCorrienteService
{
    public function findAll(Request $request)
    {
        //--
    }

    // public function findById(Request $request)
    // {

    //     $cliente = Cliente::where('id', $request->id)->first();

    //     $SQL = "SELECT id, cliente, comprobante, numero, fecha, total, link,
    //     (SELECT SUM(total) 
    //      FROM (
    //          SELECT total
    //          FROM tienda.recibo
    //          WHERE anulado = 0 AND fecha >= '2015-09-01' AND cliente = $request->id
    //          UNION
    //          SELECT total * -1
    //          FROM tienda.reintegro
    //          WHERE anulado = 0 AND fecha >= '2015-09-01' AND cliente = $request->id
    //          UNION
    //          SELECT total * -1
    //          FROM tienda.invoice
    //          WHERE anulada = 0 AND fecha >= '2015-09-01' AND cliente = $request->id
    //      ) AS subtotals) AS total_general
    //     FROM
    //     (
    //         SELECT id, cliente, 'RECIBO' AS comprobante, id AS numero, fecha, total, CONCAT('/recibo/', id, '.pdf') AS link
    //         FROM tienda.recibo
    //         WHERE anulado = 0 AND fecha >= '2015-09-01' AND cliente = $request->id
    //         UNION
    //         SELECT id, cliente, 'REINTEGRO' AS comprobante, 'id' AS numero, fecha, (total * -1) AS total, CONCAT('/reintegro/', id, '.pdf') AS link
    //         FROM tienda.reintegro
    //         WHERE anulado = 0 AND fecha >= '2015-09-01' AND cliente = $request->id
    //         UNION
    //         SELECT id, cliente, 'INVOICE' AS comprobante, id AS numero, fecha, (total * -1) AS total, CONCAT('/invoice/', id, '.pdf') AS link
    //         FROM tienda.invoice
    //         WHERE anulada = 0 AND fecha >= '2015-09-01' AND cliente = $request->id
    //     ) AS subconsulta
    //     ORDER BY cliente ASC, numero ASC, comprobante DESC";

    //     $cuentaCorriente = DB::select($SQL);

    //     $primerRegistro = $cuentaCorriente[0];

    //     $foo = [
    //         "results" => $cuentaCorriente,
    //         "clienteNombre" => $cliente->nombre,
    //         "total" => $primerRegistro->total_general
    //     ];

    //     if (!$cuentaCorriente) {
    //         return response()->json(['error' => 'CuentaCorriente not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     return response()->json($foo, Response::HTTP_OK);
    // }

    public function findById(Request $request)
    {

        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        $cliente = Cliente::where('id', $request->id)->first();

        $cuentaCorriente = DB::table(DB::raw("({$this->getSubQuery($request->id)}) as subconsulta"))
            ->select('id', 'cliente', 'comprobante', 'numero', 'fecha', 'total', 'link')
            ->orderBy('cliente', 'ASC')
            ->orderBy('numero', 'ASC')
            ->orderBy('comprobante', 'DESC')
            ->paginate($perPage, ['*'], 'page', $page); // Puedes ajustar el número de resultados por página según tus necesidades

        $results = [
            'status' => Response::HTTP_OK,
            'total' => $cuentaCorriente->total(),
            'cantidad_por_pagina' => $cuentaCorriente->perPage(),
            'pagina' => $cuentaCorriente->currentPage(),
            'cantidad_total' => $cuentaCorriente->total(),
            'results' => $cuentaCorriente->items(),
            "clienteNombre" => $cliente->nombre,
            "total" => $cuentaCorriente->total()
        ];

        if ($cuentaCorriente->isEmpty()) {
            return response()->json(['error' => 'CuentaCorriente not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($results, Response::HTTP_OK);
    }

    private function getSubQuery($clientId)
    {
        return "SELECT id, cliente, 'RECIBO' AS comprobante, id AS numero, fecha, total, CONCAT('/recibo/', id, '.pdf') AS link
            FROM tienda.recibo
            WHERE anulado = 0 AND fecha >= '2015-09-01' AND cliente = $clientId
            UNION
            SELECT id, cliente, 'REINTEGRO' AS comprobante, 'id' AS numero, fecha, (total * -1) AS total, CONCAT('/reintegro/', id, '.pdf') AS link
            FROM tienda.reintegro
            WHERE anulado = 0 AND fecha >= '2015-09-01' AND cliente = $clientId
            UNION
            SELECT id, cliente, 'INVOICE' AS comprobante, id AS numero, fecha, (total * -1) AS total, CONCAT('/invoice/', id, '.pdf') AS link
            FROM tienda.invoice
            WHERE anulada = 0 AND fecha >= '2015-09-01' AND cliente = $clientId";
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
