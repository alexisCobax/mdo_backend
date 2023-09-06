<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Fotoproducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class FotoproductoService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Fotoproducto::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Fotoproducto::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        switch (strtolower($request->action)) {
            case 'principal':
                try {
                    $producto = Producto::find($request->idProducto);

                    if ($producto) {
                        $producto->update(['imagenPrincipal' => $request->idImagen]);

                        return response()->json($producto, Response::HTTP_OK);
                    } else {
                        return response()->json(['error' => 'Producto not found'], Response::HTTP_NOT_FOUND);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;

            case 'eliminar':
                try {
                    $producto = FotoProducto::find($request->idImagen);
                    if ($producto) {
                        File::delete(storage_path('app/public/images/' . $request->idImagen . '.' . env('EXTENSION_IMAGEN_PRODUCTO')), true);
                        $producto->delete();

                        return response()->json($producto, Response::HTTP_OK);
                    } else {
                        return response()->json(['error' => 'Failed to delete Fotoproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
        }

        $data = $request->all();
        $fotoproducto = Fotoproducto::create($data);

        if (!$fotoproducto) {
            return response()->json(['error' => 'Failed to create Fotoproducto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($fotoproducto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $fotoproducto = Fotoproducto::find($request->id);

        if (!$fotoproducto) {
            return response()->json(['error' => 'Fotoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $fotoproducto->update($request->all());
        $fotoproducto->refresh();

        return response()->json($fotoproducto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $fotoproducto = Fotoproducto::find($request->id);

        if (!$fotoproducto) {
            return response()->json(['error' => 'Fotoproducto not found'], Response::HTTP_NOT_FOUND);
        }

        $fotoproducto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
