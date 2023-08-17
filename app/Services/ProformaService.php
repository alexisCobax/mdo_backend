<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Transformers\Pdf\FindByIdTransformer;

class ProformaService
{
    public function findAll(Request $request)
    {
        //--
    }

    public function findById(Request $request)
    {

        $pedido = Pedido::find($request->id)->first();

        $tranformer = new FindByIdTransformer();
        $pedido_transformado = $tranformer->transform($pedido);

        dd($pedido_transformado);
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
