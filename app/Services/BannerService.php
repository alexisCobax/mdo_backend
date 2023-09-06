<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BannerService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Banner::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Banner::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $banner = Banner::create($data);

        if (!$banner) {
            return response()->json(['error' => 'Failed to create Banner'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($banner, Response::HTTP_OK);
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
