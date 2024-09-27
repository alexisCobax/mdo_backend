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

        // Log de inicio con la fecha y hora actual
        Log::info('Inicio del comando para stock externo: ' . date('Y-m-d H:i:s'));

        $proceso = true;
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

            Log::info('Empezando a Procesar: ' . date('Y-m-d H:i:s'));
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
                        Log::error('Error al obtener datos de la API en la página ' . $page);
                        $proceso = false;
                        break;
                    }
                }
            } while ($totalFetched < $totalResults);

            if ($proceso) {
                Log::info('Fin del comando: ' . date('Y-m-d H:i:s'));

                $this->processStock();
            }
            $this->output->progressFinish();
        } else {
            Log::error('Error al obtener datos de la API ' . date('Y-m-d H:i:s'));
        }

        // Log de fin con la fecha y hora actual
        Log::info('Fin del comando para stock externo: ' . date('Y-m-d H:i:s'));
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
                    'Upc' => 'N' . $product['Upc'],
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
                        'nombre' => $marca->Brand,
                        'MostrarEnWeb' => 1,
                        'propia' => 0,
                        'VIP' => 0,
                        'suspendido' => 0,
                        'logo' => ''
                    ]);
                }

                $productos = DB::select('SELECT stockExterno.Upc,
                            stockExterno.Name,
                            stockExterno.Price,
                            stockExterno.Brand,
                            stockExterno.Images,
                            stockExterno.Size,
                            stockExterno.Color,
                            stockExterno.AvailableQuantity,
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

                    $color = isset($producto->Color) ? $producto->Color : '';
                    $size = isset($producto->Size) ? $producto->Size : '';
                    $name = isset($producto->Name) ? $producto->Name : '';
                    $brand = isset($producto->Brand) ? $producto->Brand : '';
                    $stock = isset($producto->AvailableQuantity) ? $producto->AvailableQuantity : 0;
                    $nombre = $brand . ' ' . $name . ' ' . $size . ' ' . $color;
                    $costo = $producto->Price;
                    $precio = number_format($producto->Price + ($producto->Price * 0.60), 2); //este 60% es a pedido del cliente

                    $SQL = 'INSERT INTO
                        producto
                        (nombre,
                        marca,
                        precio,
                        suspendido,
                        stock,
                        codigo,
                        color,
                        tamano,
                        costo,
                        proveedorExterno)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                    DB::insert($SQL, [
                        $nombre,
                        $marcaId,
                        $precio,
                        0,
                        $stock,
                        $producto->Upc,
                        $color,
                        $size,
                        $costo,
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
                LEFT JOIN marcaproducto ON stockExterno.Brand=marcaproducto.nombre
                SET producto.stock = stockExterno.availableQuantity, producto.proveedorExterno="nywd",
                producto.borrado=NULL, producto.marca=marcaproducto.id
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
