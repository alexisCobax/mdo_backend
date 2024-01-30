<?php

namespace App\Services;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\PaginateHelper;
use App\Helpers\ArrayToXlsxHelper;
use App\Filters\Prospectos\ProspectosFilters;
use App\Models\Prospecto;

class ProspectoService
{
    public function findAll(Request $request)
    {
        try {
            $data = ProspectosFilters::getPaginateProspectos($request, Cliente::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los productos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findById(Request $request)
    {
        $data = Cliente::find($request->id);

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $data = $request->all();
        $prospecto = Cliente::create($data);

        if (!$prospecto) {
            return response()->json(['error' => 'Failed to create Prospecto'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($prospecto, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $prospecto = Cliente::find($request->id);

        if (!$prospecto) {
            return response()->json(['error' => 'Prospecto not found'], Response::HTTP_NOT_FOUND);
        }

        $prospecto->update($request->all());
        $prospecto->refresh();

        return response()->json($prospecto, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $prospecto = Cliente::find($request->id);

        if (!$prospecto) {
            return response()->json(['error' => 'Prospecto not found'], Response::HTTP_NOT_FOUND);
        }

        $prospecto->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }

    public function excel(Request $request)
    {

        //                 // Obtén los parámetros del filtro
        //                 $id = $request->input('id');
        //                 $nombre = $request->input('nombre');
        //                 $email = $request->input('email');

        // $model = new Prospecto;

        

        // // Aplica los filtros si se proporcionan
        // if ($id) {
        //     if($id!='undefined'){
        //     $query->id($id);
        //     }
        // }
        // if ($nombre) {
        //     $query->nombre($nombre);
        // }
        // if ($email) {
        //     $query->email($email);
        // }

        // $prospectos = $model->select('id', 'nombre')->where('prospecto', 1)->get()->toArray();

        // Obtén los parámetros del filtro
        $id = $request->input('id');
        $nombre = $request->input('nombre');
        $email = $request->input('email');
        $telefono = $request->input('telefono');
        $contacto = $request->input('contacto');

        // Crea una nueva instancia del modelo Prospecto
        $model = new Prospecto;

        // Inicia la construcción de la consulta utilizando el modelo
        $query = $model->select('id', 'fecha', 'nombre', 'email, telefono, contacto')->where('prospecto', 1);

        // Aplica los filtros si se proporcionan
        if ($id && $id != 'undefined') {
            $query->where('id', $id);
        }

        if ($nombre) {
            $query->where('nombre', $nombre);
        }

        if ($email) {
            $query->where('email', $email);
        }
        if ($telefono) {
            $query->telefono($telefono);
        }
        if ($contacto) {
            $query->contacto($contacto);
        }

        // Ejecuta la consulta y obtén los resultados en forma de array
        $prospectos = $query->get()->toArray();

        dd($prospectos);

        try {
            return ArrayToXlsxHelper::getXlsx([], $prospectos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
