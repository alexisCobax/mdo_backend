<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use App\Filters\Banners\BannersFilters;
use Illuminate\Support\Facades\Storage;

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

        if(!$request->tipo){
            return response()->json("Tipo es obligatorio", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $tipo = $tipobanner->find($request->tipo)->first();

        $imagenOriginal = $request->file('imagen');
        $pathOriginal = $imagenOriginal->store('public/');

        $imagen = Image::make(storage_path("app/$pathOriginal"))
            ->resize($tipo->ancho, $tipo->alto)
            ->save(storage_path("app/public/banners/" . date('YmdHis') . ".jpg"));

        Storage::delete($pathOriginal);

        $banner = new Banner;
        $banner->tipoUbicacion = $request->tipo;
        $banner->codigo = '';
        $banner->suspendido = 0;
        $banner->orden = $request->orden;
        $banner->tipoArchivo = 'JPG';
        $banner->link = $request->link;
        $banner->nombre = $request->nombre;
        $banner->save();

        if (!$imagen) {
            return response()->json(['error' => 'Failed to create Banner'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json("Imagen generada correctamente", Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $banner = Banner::find($request->id);

        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], Response::HTTP_NOT_FOUND);
        }

        $banner->update($request->all());
        $banner->refresh();

        return response()->json($banner, Response::HTTP_OK);
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
