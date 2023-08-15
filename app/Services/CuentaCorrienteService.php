<?php

namespace App\Services;

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
        $SQL = "SELECT id, cliente, comprobante, numero, fecha, total, link
        FROM
        (
            SELECT id, cliente, 'RECIBO' AS comprobante, id AS numero, fecha, total, CONCAT('/recibo/', id, '.pdf') AS link
            FROM mdo.recibo
            WHERE anulado = 0 AND fecha >= 2015-09-01 AND cliente = $request->id
            UNION
            SELECT id, cliente, 'REINTEGRO' AS comprobante, 'id' AS numero, fecha, (total * -1) AS total, CONCAT('/reintegro/', id, '.pdf') AS link
            FROM mdo.reintegro
            WHERE anulado = 0 AND fecha >= 2015-09-01 AND cliente = $request->id
            UNION
            SELECT id, cliente, 'INVOICE' AS comprobante, id AS numero, fecha, (total * -1) AS total, CONCAT('/invoice/', id, '.pdf') AS link
            FROM mdo.invoice
            WHERE anulada = 0 AND fecha >= 2015-09-01 AND cliente = $request->id
        ) AS subconsulta
        ORDER BY cliente ASC, numero ASC, comprobante DESC";

        $cuentaCorriente = DB::select($SQL);

        if (!$cuentaCorriente) {
            return response()->json(['error' => 'CuentaCorriente not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($cuentaCorriente, Response::HTTP_OK);
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
