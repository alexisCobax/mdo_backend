<?php

namespace App\Filters\Clientes;

use App\Transformers\Cliente\FindAllTransformer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class ClientesFilters
{
    public static function getPaginateClientes($request, $model)
    {
        // Obtén los parámetros de la solicitud
        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        // Obtén los parámetros del filtro
        $id = $request->input('id');
        $nombre = $request->input('nombre');
        $email = $request->input('email');
        $telefono = $request->input('telefono');
        $contacto = $request->input('contacto');

        // Inicializa la consulta utilizando el modelo
        $query = $model::query()->where('prospecto', 0);

        // Aplica los filtros si se proporcionan
        if ($id) {
            if ($id != 'undefined') {
                $query->id($id);
            }
        }
        if ($nombre) {
            $query->nombre($nombre);
        }
        if ($email) {
            $query->email($email);
        }

        if ($telefono) {
            $query->telefono($telefono);
        }

        if ($contacto) {
            $query->contacto($contacto);
        }

        // Realiza la paginación de la consulta
        $data = $query->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Crea una instancia del transformer
        $transformer = new FindAllTransformer();

        // Transforma cada usuario individualmente
        $clientesTransformados = $data->map(function ($clientes) use ($transformer) {
            return $transformer->transform($clientes);
        });

        $status = $clientesTransformados->count() ? Response::HTTP_OK : Response::HTTP_NOT_FOUND;

        // Crea la respuesta personalizada
        $response = [
            'status' => $status,
            'total' => $data->total(),
            'cantidad_por_pagina' => $data->perPage(),
            'pagina' => $data->currentPage(),
            'cantidad_total' => $data->total(),
            'results' => $clientesTransformados,
        ];

        // Devuelve la respuesta
        return response()->json($response);
    }

    public static function getPaginateClientesToVendedores($request)
    {


        $page = $request->input('pagina', env('PAGE'));
        $perPage = $request->input('cantidad', env('PER_PAGE'));

        $id = $request->input('id');
        $nombre = $request->input('nombre');
        $email = $request->input('email');
        $telefono = $request->input('telefono');
        $contacto = $request->input('contacto');

        // Armamos condiciones dinámicamente
        $where = "WHERE cliente.prospecto = 0";
        $params = [];

        if ($id && $id !== 'undefined') {
            $where .= " AND cliente.id = ?";
            $params[] = $id;
        }
        if ($nombre) {
            $where .= " AND cliente.nombre LIKE ?";
            $params[] = "%{$nombre}%";
        }
        if ($email) {
            $where .= " AND cliente.email LIKE ?";
            $params[] = "%{$email}%";
        }
        if ($telefono) {
            $where .= " AND cliente.telefono LIKE ?";
            $params[] = "%{$telefono}%";
        }
        if ($contacto) {
            $where .= " AND cliente.contacto LIKE ?";
            $params[] = "%{$contacto}%";
        }

        DB::statement("SET lc_time_names = 'es_ES'");

        // Total de registros
        $total = DB::selectOne("SELECT COUNT(*) as total FROM cliente LEFT JOIN pais ON cliente.pais = pais.id LEFT JOIN usuario ON usuario.id = cliente.asesor $where", $params)->total;

        $SQL1 = "SELECT 
                *, 
                cliente.id AS clienteId,
                cliente.nombre as clienteNombre,
                DATE_FORMAT(fechaAlta, '%d-%b-%Y') AS fechaAlta, 
                pais.nombre AS paisNombre,
                IF(cliente.asesor IS NULL, '', usuario.nombre) AS asesorNombre
                FROM 
                cliente 
                LEFT JOIN pais ON cliente.pais = pais.id
                LEFT JOIN usuario ON usuario.id = cliente.asesor
                $where ORDER BY cliente.id 
                DESC LIMIT ? OFFSET ?";

        // Paginación
        $offset = ($page - 1) * $perPage;

        $params[] = (int) $perPage;
        $params[] = (int) $offset;

        $data = DB::select($SQL1, $params);

        // Transformar resultados
        // $transformer = new FindAllTransformer();
        // $clientesTransformados = collect($data)->map(function ($cliente) use ($transformer) {
        //     return $transformer->transform($cliente);
        // });

        // $status = $clientesTransformados->count()
        //     ? Response::HTTP_OK
        //     : Response::HTTP_NOT_FOUND;

        $response = [
            'status' => 200,
            'total' => $total,
            'cantidad_por_pagina' => (int) $perPage,
            'pagina' => (int) $page,
            'cantidad_total' => $total,
            'results' => $data,
        ];

        return response()->json($response);
    }
}
