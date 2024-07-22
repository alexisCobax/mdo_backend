<?php

namespace App\Services;

use App\Filters\Banners\BannersFilters;
use App\Models\Banner;
use App\Models\Tipobanner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BannerService
{
    public function findAll(Request $request)
    {
        try {
            $data = BannersFilters::getPaginateBanners($request, Banner::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los sliders'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Banner::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request, $tipobanner)
    {

        if (!$request->tipo) {
            return response()->json('Tipo es obligatorio', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $banner = new Banner;
        $banner->tipoUbicacion = $request->tipo;
        $banner->codigo = '';
        $banner->suspendido = 0;
        $banner->orden = $request->orden;
        $banner->tipoArchivo = 'JPG';
        $banner->link = $request->link;
        $banner->nombre = $request->nombre;
        $banner->texto1 = $request->textoPrincipal;
        $banner->texto2 = $request->textoSecundario;
        $banner->save();

        $tipo = $tipobanner->find($request->tipo)->first();

        $imagenOriginal = $request->file('imagen');
        $pathOriginal = $imagenOriginal->store('public/');

        $imagen = Image::make(storage_path("app/$pathOriginal"))
            ->resize($tipo->ancho, $tipo->alto)
            ->save(storage_path('app/public/banners/' . $banner->id . '.jpg'));

        Storage::delete($pathOriginal);

        if (!$imagen) {
            return response()->json(['error' => 'Failed to create Banner'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json('Imagen generada correctamente', Response::HTTP_OK);
    }

    // public function update(Request $request)
    // {
    //     $banner = Banner::find($request->id);

    //     if (!$banner) {
    //         return response()->json(['error' => 'Banner not found'], Response::HTTP_NOT_FOUND);
    //     }

    //     $banner->update($request->all());
    //     $banner->refresh();

    //     return response()->json($banner, Response::HTTP_OK);
    // }

    public function update(Request $request)
{

    // Verificar si el tipo de banner es obligatorio
    if (!$request->tipoUbicacion) {
        return response()->json('Tipo es obligatorio', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Buscar el banner existente
    $banner = Banner::find($request->id);
    if (!$banner) {
        return response()->json('Banner no encontrado', Response::HTTP_NOT_FOUND);
    }

    // Actualizar los datos del banner
    $banner->tipoUbicacion = $request->tipoUbicacion;
    $banner->codigo = $request->codigo ?? $banner->codigo;
    $banner->suspendido = $request->suspendido ?? $banner->suspendido;
    $banner->orden = $request->orden ?? $banner->orden;
    $banner->tipoArchivo = 'JPG';
    $banner->link = $request->link ?? $banner->link;
    $banner->nombre = $request->nombre ?? $banner->nombre;
    $banner->texto1 = $request->textoPrincipal;
    $banner->texto2 = $request->textoSecundario;
    $banner->save();

    // Obtener el tipo de banner para las dimensiones de la imagen
    $tipo = Tipobanner::find($request->tipoUbicacion)->first();

    if ($request->hasFile('imagen')) {
        // Eliminar la imagen antigua
        $oldImagePath = storage_path('app/public/banners/' . $banner->id . '.jpg');
        if (Storage::exists('public/banners/' . $banner->id . '.jpg')) {
            Storage::delete('public/banners/' . $banner->id . '.jpg');
        }
        //echo 1;die;
        // Subir y procesar la nueva imagen
        $imagenOriginal = $request->file('imagen');
        $pathOriginal = $imagenOriginal->store('public/');

        $imagen = Image::make(storage_path("app/$pathOriginal"))
            ->resize($tipo->ancho, $tipo->alto)
            ->save(storage_path('app/public/banners/' . $banner->id . '.jpg'));

        // Eliminar la imagen temporal
        Storage::delete($pathOriginal);

        if (!$imagen) {
            return response()->json(['error' => 'Failed to update Banner image'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    return response()->json('Banner actualizado correctamente', Response::HTTP_OK);
}


    public function delete(Request $request)
    {
        $banner = Banner::find($request->id);

        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], Response::HTTP_NOT_FOUND);
        }

        $banner->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
