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
                    'AvailableQuantity' => $product['AvailableQuantity'],
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

        try {
            // Manejo automático de transacción (incluye commit o rollback)
            DB::transaction(function () {

                // Limpiar el stock para el proveedor "nywd"
                DB::update('UPDATE producto SET stock = 0 WHERE proveedorExterno = ?', ['nywd']);

                // Actualizar precio y stock
                DB::update('
                    UPDATE producto
                    LEFT JOIN stockExterno ON stockExterno.sku = producto.codigo
                    SET producto.precio = stockExterno.price,
                        producto.stock = stockExterno.availableQuantity
                    WHERE stockExterno.sku IS NOT NULL
                ');

                // Insertar productos nuevos que no existen en la tabla "producto"
                DB::insert('
                    INSERT INTO producto (codigo, nombre, marca, imagen)
                    SELECT t.sku, t.nombre, t.MarcasVehiculo, t.imagen
                    FROM stockExterno t
                    LEFT JOIN producto p ON t.sku = p.codigo
                    WHERE p.codigo IS NULL
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
