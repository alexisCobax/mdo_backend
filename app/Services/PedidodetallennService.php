<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Pedidodetallenn;
use App\Repositories\Pedidos\PedidoDetalleNNRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PedidodetallennService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Pedidodetallenn::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //MIGRADO A REPOSITORIOS
    public function findById(Request $request)
    {
        $repository = new PedidoDetalleNNRepository;
        $pedidodetallenn = $repository->findPedidoDetalleNNById($request->id);

        if (!$pedidodetallenn) {
            return response()->json(['error' => 'No se encuentra pedido'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedidodetallenn, Response::HTTP_OK);
    }

    //MIGRADO A REPOSITORIOS
    public function findByPedidoId(Request $request)
    {
        $repository = new PedidoDetalleNNRepository;
        $pedidodetallenn = $repository->findPedidoDetalleNNByPedidoId($request->id);

        if (!$pedidodetallenn) {
            return response()->json(['error' => 'No se encuentra pedido'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedidodetallenn, Response::HTTP_OK);
    }

    //MIGRADO A REPOSITORIOS
    public function create(Request $request)
    {
        $repository = new PedidoDetalleNNRepository;
        $pedidodetallenn = $repository->createPedidoDetalleNN($request);

        if (!$pedidodetallenn) {
            return response()->json(['error' => 'Failed to create Pedidodetallenn'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedidodetallenn, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $pedidodetallenn = Pedidodetallenn::find($request->id);

        if (!$pedidodetallenn) {
            return response()->json(['error' => 'Pedidodetallenn not found'], Response::HTTP_NOT_FOUND);
        }

        $pedidodetallenn->update($request->all());
        $pedidodetallenn->refresh();

        return response()->json($pedidodetallenn, Response::HTTP_OK);
    }

    //MIGRADO A REPOSITORIOS
    public function delete(Request $request, $id)
    {
        $repository = new PedidoDetalleNNRepository;
        $pedidodetallenn = $repository->deletePedidoDetalleNN($request, $id);

        if (!$pedidodetallenn) {
            return response()->json(['error' => 'Failed to delete Pedidodetallenn'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($pedidodetallenn, Response::HTTP_OK);
    }
}
