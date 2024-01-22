<?php

namespace App\Services;

use Drawing;
use Alignment;
use App\Models\Color;
use PHPExcel_IOFactory;
use App\Models\Producto;
use App\Models\TmpImagenes;
use App\Helpers\ExcelHelper;
use App\Models\Fotoproducto;
use App\Models\Tipoproducto;
use App\Models\TmpProductos;
use Illuminate\Http\Request;
use App\Models\Marcaproducto;
use Illuminate\Http\Response;
use PHPExcel_Worksheet_Drawing;
use App\Models\Materialproducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PHPExcel_Worksheet_MemoryDrawing;
use Maatwebsite\Excel\Classes\PHPExcel;
use App\Filters\Productos\ProductosFilters;
use PHPExcel_Style_Alignment;

class ExcelToJsonService
{
    /**
     * procesar.
     *
     * @return void
     */
    public function procesar(Request $request)
    {

        $response = ExcelHelper::upload($request);
        $jsonData = $response->getContent();
        $archivo = json_decode($jsonData, true);

        TmpProductos::truncate();
        TmpImagenes::truncate();
        File::deleteDirectory(storage_path('app/public/compras/productos/'), true);
        $spreadsheet = PHPExcel_IOFactory::load(storage_path('app/public/' . $archivo['path']));
        $sheet = $spreadsheet->getActiveSheet();
        $drawingCollection = $sheet->getDrawingCollection();
        $skipFirstRow = true;
        $errorMessages = [];
        $i = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $i++;
            $errorMessages = [];

            if ($skipFirstRow) {
                $skipFirstRow = false;
                continue;
            }

            $sku = $sheet->getCell('A' . $row->getRowIndex())->getValue();
            $marca = $sheet->getCell('B' . $row->getRowIndex())->getValue();
            $nombre = $sheet->getCell('C' . $row->getRowIndex())->getValue();
            $tipo = $sheet->getCell('D' . $row->getRowIndex())->getValue();
            $color_fabricante = $sheet->getCell('E' . $row->getRowIndex())->getValue();
            $color_generico = $sheet->getCell('F' . $row->getRowIndex())->getValue();
            $tamanio = $sheet->getCell('G' . $row->getRowIndex())->getValue();
            $material = $sheet->getCell('H' . $row->getRowIndex())->getValue();
            $cantidad = $sheet->getCell('I' . $row->getRowIndex())->getValue();
            $estuche = $sheet->getCell('J' . $row->getRowIndex())->getValue();
            $costo = $sheet->getCell('K' . $row->getRowIndex())->getValue();
            $precio_venta = $sheet->getCell('L' . $row->getRowIndex())->getValue();
            // $upc = $sheet->getCell('M' . $row->getRowIndex())->getValue();

            if (!empty($sku) && is_numeric($sku)) {

                if (empty($marca)) {
                    $errorMessages[] = 'El campo Marca esta vacio Celda B' . $row->getRowIndex();
                }

                if (empty($nombre)) {
                    $errorMessages[] = 'El campo Nombre esta vacio Celda C' . $row->getRowIndex();
                }

                if (empty($tipo)) {
                    $errorMessages[] = 'El campo Tipo esta vacio Celda D' . $row->getRowIndex();
                }

                if (empty($color_fabricante)) {
                    $errorMessages[] = 'El campo Color Fabricante esta vacio Celda E' . $row->getRowIndex();
                }

                if (empty($color_generico)) {
                    $errorMessages[] = 'El campo Color Generico esta vacio Celda F' . $row->getRowIndex();
                }

                if (empty($tamanio)) {
                    $errorMessages[] = 'El campo Tamaño esta vacio Celda G' . $row->getRowIndex();
                }

                if (empty($material)) {
                    $errorMessages[] = 'El campo Material esta vacio Celda H' . $row->getRowIndex();
                }

                if (!is_numeric($cantidad)) {
                    $errorMessages[] = 'El campo Cantidad es invalido Celda I' . $row->getRowIndex();
                }

                if (empty($estuche)) {
                    $errorMessages[] = 'El campo Estuche esta vacio Celda J' . $row->getRowIndex();
                }

                if (!is_numeric($costo)) {
                    $errorMessages[] = 'El campo Costo esta vacio o es invalido en Celda K' . $row->getRowIndex();
                }

                if (empty($precio_venta)) {
                    $errorMessages[] = 'El campo Precio Venta esta vacio Celda L' . $row->getRowIndex();
                }

                // if (empty($upc)) {
                //     $errorMessages[] = 'El campo UPC esta vacio Celda M' . $row->getRowIndex();
                // }

                $tmpProductos = new TmpProductos();
                $tmpProductos->sku = $sku;
                $tmpProductos->marca = $marca;
                $tmpProductos->nombre = $nombre;
                $tmpProductos->tipo = $tipo;
                $tmpProductos->color_fabricante = $color_fabricante;
                $tmpProductos->color_generico = $color_generico;
                $tmpProductos->tamanio = $tamanio;
                $tmpProductos->material = $material;
                $tmpProductos->cantidad = $cantidad;
                $tmpProductos->estuche = $estuche;
                $tmpProductos->costo = $costo;
                $tmpProductos->precio_venta = $precio_venta;
                // $tmpProductos->upc = $upc;
                $tmpProductos->image = 'N' . $row->getRowIndex();
                if ($errorMessages) {
                    $tmpProductos->error = json_encode($errorMessages);
                }
                if ($cantidad > 0) {
                    $tmpProductos->save();
                }
            } else {
                break;
            }
        }

        $errores = TmpProductos::whereNotNull('error')->get(['error']);
        if ($errores->isNotEmpty()) {
            return json_decode($errores);
        }

        foreach ($drawingCollection as $drawing) {

            $tmpImages = new TmpImagenes();
            $imageName = $drawing->getIndexedFilename();

            $cellAnchor = $drawing->getCoordinates();
            $imageContents = $drawing->getImageResource();

            $destinationPath = storage_path('app/public/compras/productos/' . $imageName);
            imagepng($imageContents, $destinationPath);
            $tmpImages->imagen = $cellAnchor;
            $tmpImages->nombre = $imageName;
            $tmpImages->save();
        }

        File::delete(storage_path('app/public/' . $archivo['path']), true);

        return $this->buscoCoincidencias();
    }

    /**
     * buscoCoincidencias.
     *
     * busco productos con SKU iguales
     *
     * @return void
     */
    public function buscoCoincidencias()
    {

        $skusNoEncontrados = TmpProductos::leftJoin('producto', 'tmp_productos.sku', '=', 'producto.codigo')
            ->whereNull('producto.codigo')
            ->get(['tmp_productos.*']);

        foreach ($skusNoEncontrados as $tmpProducto) {
            $producto = new Producto();
            $producto->nombre = $tmpProducto->nombre;
            $producto->descripcion = $tmpProducto->nombre.' - '.$tmpProducto->tamanio.' - '.$tmpProducto->color_fabricante;
            $producto->tipo = $this->BuscarTipo($tmpProducto->tipo);
            $producto->categoria = 1;
            $producto->marca = $this->BuscarMarcas($tmpProducto->marca);
            $producto->material = $this->BuscarMateriales($tmpProducto->material);
            $producto->estuche = $this->BuscarEstuches($tmpProducto->estuche);
            $producto->sexo = 0;
            $producto->proveedor = 0;
            $producto->precio = $tmpProducto->precio_venta;
            $producto->suspendido = 0;
            $producto->comision = 1;
            $producto->stock = 0;
            $producto->stockMinimo = 0;
            $producto->codigo = $tmpProducto->sku;
            $producto->alarmaStockMinimo = 0;
            $producto->color = $tmpProducto->color_fabricante;
            $producto->tamano = $tmpProducto->tamanio;
            $producto->ubicacion = '';
            $producto->grupo = 0;
            $producto->pagina = 0;
            $producto->costo = $tmpProducto->costo;
            $producto->BestBrasil = 0;
            $producto->posicion = 0;
            $producto->stockRoto = 0;
            $producto->ultimoIngresoDeMercaderia = NOW();
            $producto->ultimaVentaDeMercaderia = NOW();
            $producto->genero = 1;
            $producto->imagenPrincipal = 0;
            // $producto->UPCreal = $tmpProducto->upc;
            $producto->mdoNet = 1;
            $producto->jet = 0;
            $producto->precioJet = '0.00';
            $producto->stockJet = 0;
            $producto->multipack = 0;
            $producto->nodeJet = 0;
            $producto->nombreEN = null;
            $producto->descripcionEN = null;
            $producto->peso = 10;
            $producto->enviadoAJet = false;
            $producto->stockFalabella = 0;
            $producto->precioFalabella = 0;
            $producto->verEnFalabella = false;
            $producto->enviadoAFalabella = false;
            $producto->categoriaFalabella = 0;
            $producto->marcaFalabella = null;
            $producto->descripcionFalabella = null;
            $producto->precioPromocional = 0;
            $producto->destacado = true;
            $producto->largo = 0;
            $producto->alto = 0;
            $producto->ancho = 0;
            $producto->descripcionLarga = '';
            $producto->colorPrincipal = $this->BuscarColores($tmpProducto->color_generico);
            $producto->colorSecundario = 0;
            $producto->save();

            // if ($tmpProducto->image) {
            //     $this->procesarImagenes($tmpProducto->image, $producto->id);
            // }
        }

        $productosProcesados = DB::table('tmp_productos as tp')
            ->select('p.id', 'p.nombre', 'tp.cantidad', 'tp.costo')
            ->leftJoin('producto as p', 'tp.sku', '=', 'p.codigo')
            ->get();

        $productosTransformados = [];

        foreach ($productosProcesados as $pp) {
            $productosTransformados[] = [
                'compra' => 0,
                'producto' => $pp->id,
                'nombreProducto' => $pp->nombre,
                'cantidad' => $pp->cantidad,
                'precioUnitario' => $pp->costo,
                'enDeposito' => 0,
            ];
        }

        return response()->json(['data' => $productosTransformados], Response::HTTP_OK);
    }

    /**
     * procesarImagenes.
     *
     * agrego al modelo fotoProductos, traigo el id nuevo y actualizo producto.
     *
     * @param  mixed $imagen
     * @param  mixed $productoId
     * @return void
     */
    public function procesarImagenes($imagen, $productoId)
    {

        $imagen = TmpImagenes::where('imagen', $imagen)->first();
        $anterior = storage_path('app/public/compras/productos/' . $imagen->nombre);
        $nueva = storage_path('app/public/compras/productos/' . $productoId . '.jpg');
        if (rename($anterior, $nueva)) {

            $fotoProducto = new fotoProducto();
            $fotoProducto->idProducto = $productoId;
            $fotoProducto->orden = 0;
            $fotoProducto->save();

            $producto = Producto::find($productoId);
            $producto->imagenPrincipal = $fotoProducto->id;
            $producto->save();
        }
    }

    /**
     * BuscarMarcas.
     *
     * Busco marcas coindicentes sino las creo
     *
     * @param  mixed $marca
     * @return void
     */
    public function BuscarMarcas($marca)
    {
        if ($marca) {

            $marca_existente = MarcaProducto::whereRaw('LOWER(nombre) = ?', [strtolower($marca)])->first();

            if ($marca_existente) {
                return $marca_existente->id;
            } else {
                $nueva_marca = new MarcaProducto();
                $nueva_marca->nombre = $marca;
                $nueva_marca->propia = 0;
                $nueva_marca->VIP = 0;
                $nueva_marca->logo = 0;
                $nueva_marca->MostrarEnWeb = 1;
                $nueva_marca->suspendido = 0;
                $nueva_marca->save();

                return $nueva_marca->id;
            }
        }
    }

    /**
     * BuscarColores.
     *
     * Busco solores coincidentes sino los creo
     *
     * @param  mixed $color
     * @return void
     */
    public function BuscarColores($color)
    {
        if ($color) {

            $color_existente = Color::whereRaw('LOWER(nombre) = ?', [strtolower($color)])->first();

            if ($color_existente) {
                return $color_existente->id;
            } else {
                $nuevo_color = new Color();
                $nuevo_color->nombre = $color;
                $nuevo_color->suspendido = 0;
                $nuevo_color->save();

                return $nuevo_color->id;
            }
        }
    }

    /**
     * BuscarMateriales.
     *
     * Busco materiales coincidentes sino los creo
     *
     * @param  mixed $material
     * @return void
     */
    public function BuscarMateriales($material)
    {
        if ($material) {

            $material_existente = Materialproducto::whereRaw('LOWER(nombre) = ?', [strtolower($material)])->first();

            if ($material_existente) {
                return $material_existente->id;
            } else {
                $nuevo_material = new Materialproducto();
                $nuevo_material->nombre = $material;
                $nuevo_material->suspendido = 0;
                $nuevo_material->save();

                return $nuevo_material->id;
            }
        }
    }

    public function BuscarEstuches($estuche)
    {
        $estuche = strtolower($estuche);
        $estuche = str_replace(' ', '', $estuche);

        switch ($estuche) {
            case 'si':
                return 1;
            case 'no':
                return 0;
            default:
                return 2;
        }
    }

    public function BuscarTipo($tipo)
    {
        if ($tipo) {

            $tipo_existente = Tipoproducto::whereRaw('LOWER(nombre) = ?', [strtolower($tipo)])->first();

            if ($tipo_existente) {
                return $tipo_existente->id;
            } else {
                $nuevo_tipo = new TipoProducto();
                $nuevo_tipo->nombre = $tipo;
                $nuevo_tipo->CantidadMinima = 0;
                $nuevo_tipo->suspendido = 0;
                $nuevo_tipo->save();

                return $nuevo_tipo->id;
            }
        }
    }

    /**
     * generar.
     *
     * @return void
     */
    public function generarProductos($request)
    {

        $jsonResponse = ProductosFilters::getPaginateProducts($request, Producto::class);
        $response = $jsonResponse->getData(true);
        $result = $response['results'];

        $results = array_map(function ($item) {

            return [
                "codigo" => $item["codigo"],
                "nombre" => $item["nombre"],
                "categoria" => $item["categoriaNombre"],
                "marca" => $item['nombreMarca'],
                "precio" => $item['precioPromocional'] == 0 ? number_format($item['precio'], 2) : number_format($item['precioPromocional'], 2),
                "imagen" => storage_path('app/public/images/'.$item['imagenPrincipal'])
            ];
        }, $result);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        $sheet->setCellValue('A1', 'Codigo');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Categoria');
        $sheet->setCellValue('D1', 'Marca');
        $sheet->setCellValue('E1', 'Precio');
        $sheet->setCellValue('F1', 'Imagen');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(60);
        $sheet->getColumnDimension('F')->setWidth(50);

        $row = 2;

        foreach ($results as $item) {
            $sheet->setCellValue('A' . $row, $item['codigo']);
            $styleA1 = $sheet->getStyle('A1');
            $styleA1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $styleA = $sheet->getStyle('A' . $row);
            $styleA->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B' . $row, $item['nombre']);
            $styleB1 = $sheet->getStyle('B1');
            $styleB1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $styleB = $sheet->getStyle('B' . $row);
            $styleB->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValue('C' . $row, $item['categoria']);
            $styleC1 = $sheet->getStyle('C1');
            $styleC1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $styleC = $sheet->getStyle('C' . $row);
            $styleC->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValue('D' . $row, $item['marca']);
            $styleD1 = $sheet->getStyle('D1');
            $styleD1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $styleD = $sheet->getStyle('D' . $row);
            $styleD->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValue('E' . $row, $item['precio']);
            $sheet->getRowDimension($row)->setRowHeight(60);
            $styleE1 = $sheet->getStyle('E1');
            $styleE1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $styleE = $sheet->getStyle('E' . $row);
            $styleE->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            if (file_exists($item['imagen'])) {
                $drawing = new PHPExcel_Worksheet_MemoryDrawing();
                $drawing->setName('Imagen');
                $drawing->setDescription('Imagen');
                $drawing->setImageResource(imagecreatefromjpeg($item['imagen']));
                $drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                $drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                $drawing->setCoordinates('F' . $row);
                $sheet->getRowDimension($row)->setRowHeight(100);
                $drawing->setHeight(200);
                $drawing->setWidth(200);
                $drawing->setWorksheet($sheet);
                $styleE1 = $sheet->getStyle('F1');
                $styleE1->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }

            $row++;
        }

        $filename = 'productos.xls';

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');

        exit();
    }
}
