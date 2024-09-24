<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class FetchProducts extends Command
{
    protected $signature = 'fetch:products';
    protected $description = 'Fetch products from API and store them in the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $apiUrl = 'https://developer.nywd.com/api/v1/products';
        $page = 1;
        $pageSize = 500;
        $totalResults = 0;
        $totalFetched = 0;

        DB::table('stockExterno')->truncate();

        $token = $this->getToken();

        $response = Http::withToken($token)->get($apiUrl, [
            'page' => $page,
            'pageSize' => $pageSize,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $totalResults = $data['TotalResults'];


            $this->output->progressStart($totalResults);

            do {
                foreach ($data['Products'] as $product) {
                    $this->saveProductToDatabase($product);

                    $this->output->progressAdvance();
                }

                $totalFetched += count($data['Products']);
                $page++;

                if ($totalFetched < $totalResults) {
                    $response = Http::withToken($token)->get($apiUrl, [
                        'page' => $page,
                        'pageSize' => $pageSize,
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                    } else {
                        $this->error('Error al obtener datos de la API en la página ' . $page);
                        break;
                    }
                }
            } while ($totalFetched < $totalResults);

            $this->output->progressFinish();

            $this->info('Todos los productos han sido guardados.');

            $this->processStock();
        } else {
            $this->error('Error al obtener datos de la API.');
        }
    }

    public function saveProductToDatabase($product)
    {
        try {
            DB::table('stockExterno')->insert(
                [
                    'Sku' => $product['Sku'],
                    'Name' => $product['Name'],
                    'Price' => $product['Price'],
                    'Category' => $product['Category'],
                    'Brand' => $product['Brand'],
                    'Upc' => $product['Upc'],
                    'Size' => $product['Size'],
                    'BridgeSize' => $product['BridgeSize'],
                    'TempleSize' => $product['TempleSize'],
                    'EyeSize' => $product['EyeSize'],
                    'Gender' => $product['Gender'],
                    'Color' => $product['Color'],
                    'FrameColor' => $product['FrameColor'],
                    'LensColor' => $product['LensColor'],
                    'Country' => $product['Country'],
                    'AvailableQuantity' => preg_replace('/\D/', '', $product['AvailableQuantity']),
                    'Images' => json_encode($product['Images'])
                ]
            );
        } catch (\Exception $e) {
            // Log the error with details
            Log::error('Error saving product to database: ' . $e->getMessage(), [
                'product' => $product,
                'error' => $e,
            ]);
        }
    }



    function processStock()
    {
        /**La funcion hace los siguientes pasos:
         *
         * 1 limpia todo el stock de los productos del proveedor
         * 2 inserta todas las marcas que no existen en la DB
         * 3 inserta todos los productos que no existen en la DB
         * 4 inserta las imagenes de cada producto
         * 5 actualiza el stock de cada producto
         *
         **/


        try {
            // Manejo automático de transacción (incluye commit o rollback)
            DB::transaction(function () {

                // Limpiar el stock para el proveedor "nywd"
                DB::update('UPDATE producto SET stock = 0 WHERE proveedorExterno = ?', ['nywd']);

                $marcas = DB::select('SELECT DISTINCT t.Brand
                        FROM stockExterno t
                        LEFT JOIN producto p ON t.Upc = p.codigo
                        LEFT JOIN marcaproducto mp ON t.Brand=mp.nombre
                        WHERE p.codigo IS NULL AND mp.id IS NULL');

                foreach ($marcas as $marca) {

                    DB::table('marcaproducto')->insert([
                        'nombre' => $marca->Brand
                    ]);
                }

                $productos = DB::select('SELECT stockExterno.Upc,
                            stockExterno.Name,
                            stockExterno.Brand,
                            stockExterno.Images,
                            marcaproducto.id AS idMarca
                    FROM stockExterno
                        LEFT JOIN producto ON stockExterno.Upc = producto.codigo
                        LEFT JOIN marcaproducto ON stockExterno.Brand=marcaproducto.nombre
                    WHERE producto.codigo IS NULL
                ');

                // Recorrer los resultados e insertar en la tabla producto
                foreach ($productos as $producto) {

                    // Si ya existe, obtenemos el id
                    $marcaId = $producto->idMarca;

                    if (isset($product->Color)) {
                        $color = $product->Color;
                    } else {
                        $color = null;
                    }

                    if (isset($product->Color)) {
                        $size = $producto->Size;
                    } else {
                        $size = null;
                    }


                    DB::insert('INSERT INTO producto (codigo, nombre, marca, color, tamano, proveedorExterno) VALUES (?, ?, ?, ?, ?, ?)', [
                        $producto->Upc,
                        $producto->Name,
                        $marcaId,
                        $color ,
                        $size,
                        "nywd"
                    ]);

                    $idProducto = DB::getPdo()->lastInsertId();

                    // Decodificar el campo Images (JSON)
                    $imagenes = json_decode($producto->Images);

                    $imagenPrincipal = 0;
                    // Insertar cada imagen en la tabla 'fotoproducto'
                    foreach ($imagenes as $imagen) {
                        DB::insert('INSERT INTO fotoproducto (idProducto, orden, url) VALUES (?, ?, ?)', [
                            $idProducto,
                            $imagen->Number,
                            $imagen->LargeImageUrl
                        ]);

                        if ($imagenPrincipal == 0) {
                            $ImagenPrincipal = DB::getPdo()->lastInsertId();
                        }
                    }

                    // Aca se guarda la imagen principal en la tabla producto
                    DB::table('producto')
                        ->where('id', $idProducto)
                        ->update(['imagenPrincipal' => $ImagenPrincipal]);
                }

                DB::update('UPDATE producto
                LEFT JOIN stockExterno ON stockExterno.Upc = producto.codigo
                SET producto.stock = stockExterno.availableQuantity
                WHERE stockExterno.Upc IS NOT NULL
            ');
            });

            return response()->json(['success' => true, 'message' => 'Precios y stock actualizados correctamente.'], 200);
        } catch (Throwable $e) {

            // Laravel ya ha hecho rollback automáticamente al entrar aquí

            // Loguear el error con detalles
            Log::error('Error actualizando precios y stock', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Devolver una respuesta con el error
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar precios y stock.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function insertStock()
    {
        $query = "
        INSERT INTO productos (codigo, nombre, marca, imagen)
        SELECT t.sku, t.nombre, t.MarcasVehiculo, t.imagen
        FROM stockExterno t
            LEFT JOIN productos p ON t.sku = p.codigo
        WHERE p.codigo IS NULL;
    ";

        DB::statement($query);

        return response()->json(['message' => 'Productos insertados correctamente']);
    }


    public function getToken()
    {

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://developer.nywd.com/api/v1/account/login', [
            'username' => 'doralice@mayoristasdeopticas.com',
            'password' => 'p>.nqms%}Wx2HR6-XUCZr#>m$CU&!=(pha7(0H>>(TSr['
        ]);

        $data = $response->json();

        return $data['AccessToken'] ?? null;
    }
}
