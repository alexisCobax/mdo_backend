<?php

namespace App\Repositories\Pedidos;

use App\Services\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Http\JsonResponse;

class PedidoDetalleNNRepository
{
    public function findPedidoDetalleNNById($id)
    {
        $SQL = "SELECT * FROM pedidodetallenn WHERE id = ?";

        $productosNN = DB::select($SQL, [$id]);

        return response()->json($productosNN);
    }

    public function findPedidoDetalleNNByPedidoId($id)
    {
        $SQL = "SELECT * FROM pedidodetallenn WHERE pedido = ? ORDER BY id DESC";

        $productosNN = DB::select($SQL, [$id]);

        return response()->json($productosNN);
    }

    public function createPedidoDetalleNN(Request $request): JsonResponse
    {
        $data = $request->validate([
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'pedido' => 'required|numeric|min:0',
            'cantidad' => 'required|numeric|min:0',
        ]);

        $SQL = "INSERT INTO
            pedidodetallenn
            (descripcion, precio, pedido, cantidad)
            VALUES
            (?, ?, ?, ?)";

        DB::insert($SQL, [
            $data['descripcion'],
            $data['precio'],
            $data['pedido'],
            $data['cantidad'],
        ]);

$id = DB::getPdo()->lastInsertId();

        $pedidoService = new PedidoService();
        $pedidoService->calcularTotal($data['pedido']);

        $userId = Auth::id();
        $fecha = now();

        $data['id'] = $id;

        // Registrar en el log mensual
        Log::channel('monthly')->info('pedidoDetalleNN POST', [
            'user_id' => $userId,
            'fecha' => $fecha,
            'data' => $data,
        ]);

        $response = $this->findPedidoDetalleNNById($id);

        return response()->json($response);
    }

    public function deletePedidodetalleNN(Request $request, $id)
    {
        $pedidoDetalle = $this->findPedidoDetalleNNById($id);
        $jsonData = json_decode($pedidoDetalle->getContent());
        $pedido = $jsonData[0]->pedido;

        $SQL = "DELETE FROM
                pedidodetallenn
                WHERE
                id = ?";
        DB::delete($SQL, [
            $id
        ]);

        $pedidoService = new PedidoService();
        $pedidoService->calcularTotal($pedido);

        $response = $this->findPedidoDetalleNNByPedidoId($pedido);

        $data = [
            'id_pedidoDetalle' => $id,
            'pedido' => $pedido
        ];

        $userId = Auth::id();
        $fecha = now();

        // Registrar en el log mensual
        Log::channel('monthly')->info('pedidoDetalleNN DELETE', [
            'user_id' => $userId,
            'fecha' => $fecha,
            'data' => $data,
        ]);

        return response()->json($response);
    }
}
