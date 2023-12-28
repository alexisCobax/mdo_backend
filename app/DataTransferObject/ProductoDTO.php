<?php

namespace App\DataTransferObject;

class ProductoDTO
{
    public $datosProducto;
    public $imagenes;

    public function __construct(array $data)
    {
        $this->datosProducto($data);
    }

    private function datosProducto(array $data)
    {

        $this->datosProducto = [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'tipo' => $data['tipo'],
            'categoria' => $data['categoria'],
            'marca' => $data['marca'],
            'material' => $data['material'],
            'estuche' => $data['estuche'],
            'sexo' => $data['sexo'],
            'proveedor' => $data['proveedor'],
            'precio' => $data['precio'],
            'suspendido' => $data['suspendido'],
            'comision' => $data['comision'],
            'stock' => $data['stock'],
            'stockMinimo' => $data['stockMinimo'],
            'codigo' => $data['codigo'],
            'alarmaStockMinimo' => $data['alarmaStockMinimo'],
            'color' => $data['color'],
            'tamano' => $data['tamano'],
            'ubicacion' => $data['ubicacion'],
            'grupo' => $data['grupo'],
            'pagina' => $data['pagina'],
            'costo' => $data['costo'],
            'bestBrasil' => $data['bestBrasil'],
            'posicion' => $data['posicion'],
            'stockRoto' => $data['stockRoto'],
            'ultimoIngresoDeMercaderia' => date("Y-m-d H:i:s"),
            'ultimaVentaDeMercaderia' => date("Y-m-d H:i:s"),
            'genero' => $data['genero'],
            'UPCreal' => $data['UPCreal'],
            'mdoNet' => $data['mdoNet'],
            'jet' => $data['jet'],
            'precioJet' => $data['precioJet'],
            'stockJet' => $data['stockJet'],
            'multipack' => $data['multipack'],
            'nodeJet' => $data['nodeJet'],
            'nombreEN' => $data['nombreEN'],
            'descripcionEN' => $data['descripcionEN'],
            'peso' => $data['peso'],
            'enviadoAJet' => $data['enviadoAJet'],
            'stockFalabella' => $data['stockFalabella'],
            'precioFalabella' => $data['precioFalabella'],
            'verEnFalabella' => $data['verEnFalabella'],
            'enviadoAFalabella' => $data['enviadoAFalabella'],
            'categoriaFalabella' => $data['categoriaFalabella'],
            'marcaFalabella' => $data['marcaFalabella'],
            'descripcionFalabella' => $data['descripcionFalabella'],
            'precioPromocional' => $data['precioPromocional'],
            'destacado' => $data['destacado'],
            'largo' => $data['largo'],
            'alto' => $data['alto'],
            'ancho' => $data['ancho'],
            'descripcionLarga' => $data['descripcionLarga'],
            'colorPrincipal' => $data['colorPrincipal'],
            'colorSecundario' => $data['colorSecundario'],
        ];
    }
}
