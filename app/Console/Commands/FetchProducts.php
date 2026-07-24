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


    /**
     * Log general de sincronización NYWD
     */
    protected function logNywd($mensaje, $data = [])
    {
        $logfile = storage_path("logs/productosnywd.log");

        $logEntry = date('Y-m-d H:i:s') . " - " . $mensaje . "\n";


        if (!empty($data)) {

            $logEntry .= json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ) . "\n";

        }


        $logEntry .= "---------------------------------------------\n\n";


        file_put_contents(
            $logfile,
            $logEntry,
            FILE_APPEND | LOCK_EX
        );
    }



    /**
     * Guarda el response completo de NYWD por página
     */
    protected function logNywdResponse($page, $response)
    {

        $directory = storage_path("logs/nywd");


        if (!is_dir($directory)) {

            mkdir(
                $directory,
                0777,
                true
            );

        }


        $logfile = $directory . "/productosnywd_pagina_" . $page . ".json";


        file_put_contents(
            $logfile,
            json_encode(
                $response,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
            LOCK_EX
        );

    }



    public function handle()
    {

        Log::info(
            'Inicio del comando para stock externo: ' .
            date('Y-m-d H:i:s')
        );


        $this->logNywd(
            'Inicio sincronización productos NYWD'
        );



        $proceso = true;


        $apiUrl = 'https://developer.nywd.com/api/v1/products';


        $page = 1;

        $pageSize = 500;


        $totalResults = 0;

        $totalFetched = 0;



        $token = $this->getToken();



        if (!$token) {


            $this->logNywd(
                'ERROR obteniendo token NYWD'
            );


            Log::error(
                'No se pudo obtener token NYWD'
            );


            return 1;

        }




        DB::table('stockExterno')->truncate();





        $response = Http::withToken($token)->get(
            $apiUrl,
            [
                'page' => $page,
                'pageSize' => $pageSize,
            ]
        );





        if ($response->successful()) {



            $data = $response->json();



            // Guardamos respuesta completa página 1
            $this->logNywdResponse(
                $page,
                $data
            );



            $totalResults = $data['TotalResults'];




            $this->logNywd(
                'Respuesta inicial API NYWD',
                [
                    'total_productos' => $totalResults,

                    'pagina' => $page,

                    'cantidad_recibida' => count($data['Products']),

                    'archivo_response' =>
                        "storage/logs/nywd/productosnywd_pagina_{$page}.json"
                ]
            );





            Log::info(
                'Empezando a Procesar: ' .
                date('Y-m-d H:i:s')
            );



            $this->output->progressStart(
                $totalResults
            );







            do {



                foreach ($data['Products'] as $product) {


                    $this->saveProductToDatabase(
                        $product
                    );


                    $this->output->progressAdvance();

                }





                $totalFetched += count(
                    $data['Products']
                );





                $this->logNywd(
                    'Página procesada NYWD',
                    [
                        'pagina' => $page,

                        'procesados' => $totalFetched,

                        'total' => $totalResults
                    ]
                );





                $page++;





                if ($totalFetched < $totalResults) {



                    $response = Http::withToken($token)->get(
                        $apiUrl,
                        [
                            'page' => $page,

                            'pageSize' => $pageSize,
                        ]
                    );






                    if ($response->successful()) {



                        $data = $response->json();



                        // Guardar response completo de cada página
                        $this->logNywdResponse(
                            $page,
                            $data
                        );





                        $this->logNywd(
                            'Nueva página descargada NYWD',
                            [
                                'pagina' => $page,

                                'cantidad_recibida' =>
                                    count($data['Products']),

                                'archivo_response' =>
                                    "storage/logs/nywd/productosnywd_pagina_{$page}.json"
                            ]
                        );




                    } else {



                        $this->logNywd(
                            'ERROR obteniendo página API NYWD',
                            [
                                'pagina' => $page,

                                'respuesta' =>
                                    $response->body()
                            ]
                        );



                        Log::error(
                            'Error al obtener datos API página ' .
                            $page
                        );



                        $proceso = false;


                        break;


                    }



                }





            } while (
                $totalFetched < $totalResults
            );






            if ($proceso) {



                $this->logNywd(
                    'Finalizando descarga API NYWD',
                    [
                        'total_procesados' =>
                            $totalFetched
                    ]
                );



                Log::info(
                    'Fin del comando: ' .
                    date('Y-m-d H:i:s')
                );



                $this->processStock();


            }





            $this->output->progressFinish();





            $this->logNywd(
                'Proceso terminado correctamente'
            );





            Log::info(
                'Comando realizado exitosamente: ' .
                date('Y-m-d H:i:s')
            );





        } else {



            $this->logNywd(
                'ERROR respuesta API NYWD',
                [
                    'status' =>
                        $response->status(),

                    'respuesta' =>
                        $response->json()
                ]
            );



            Log::error(
                'Error al obtener datos API ' .
                date('Y-m-d H:i:s')
            );

        }





        $this->logNywd(
            'Fin comando sincronización NYWD'
        );



        Log::info(
            'Fin del comando para stock externo: ' .
            date('Y-m-d H:i:s')
        );

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


            $this->logNywd(
                'ERROR guardando producto en stockExterno',
                [
                    'sku' => $product['Sku'] ?? null,

                    'nombre' => $product['Name'] ?? null,

                    'producto_completo' => $product,

                    'error' => $e->getMessage()
                ]
            );



            Log::error(
                'Error saving product to database: ' . $e->getMessage(),
                [
                    'product' => $product,
                    'error' => $e,
                ]
            );


        }
    }





    function processStock()
    {

        /**
         * Proceso:
         *
         * 1 - Limpia stock anterior NYWD
         * 2 - Inserta marcas faltantes
         * 3 - Inserta productos nuevos
         * 4 - Inserta imágenes
         * 5 - Actualiza stock y precios
         *
         */


        try {


            DB::transaction(function () {



                if (!DB::table('stockExterno')->exists()) {


                    $this->logNywd(
                        'ERROR stockExterno vacío'
                    );


                    Log::error(
                        'Tabla stockExterno sin datos ' . now()
                    );


                    abort(
                        500,
                        'La tabla stockExterno no contiene datos.'
                    );

                }




                DB::update(
                    'UPDATE producto 
                     SET stock = 0 
                     WHERE proveedorExterno = ?',
                    [
                        'nywd'
                    ]
                );





                $marcas = DB::select(
                    'SELECT DISTINCT t.Brand
                    FROM stockExterno t
                    LEFT JOIN producto p 
                        ON t.Upc = p.codigo
                    LEFT JOIN marcaproducto mp 
                        ON t.Brand = mp.nombre
                    WHERE p.codigo IS NULL 
                    AND mp.id IS NULL'
                );




                $contadorMarcas = 0;



                foreach ($marcas as $marca) {



                    DB::table('marcaproducto')->insert([

                        'nombre' => $marca->Brand,

                        'MostrarEnWeb' => 1,

                        'propia' => 0,

                        'VIP' => 0,

                        'suspendido' => 0,

                        'logo' => ''

                    ]);



                    $contadorMarcas++;

                }



                $this->logNywd(
                    'Marcas procesadas',
                    [
                        'cantidad' => $contadorMarcas
                    ]
                );






                $productos = DB::select(
                    'SELECT 
                        stockExterno.Upc,
                        stockExterno.Name,
                        stockExterno.Price,
                        stockExterno.Brand,
                        stockExterno.Images,
                        stockExterno.Size,
                        stockExterno.Color,
                        stockExterno.AvailableQuantity,
                        marcaproducto.id AS idMarca

                    FROM stockExterno

                    LEFT JOIN producto 
                        ON stockExterno.Upc = producto.codigo

                    LEFT JOIN marcaproducto 
                        ON stockExterno.Brand = marcaproducto.nombre

                    WHERE producto.codigo IS NULL'
                );





                $contadorProductos = 0;



                foreach ($productos as $producto) {



                    $contadorProductos++;



                    $marcaId = $producto->idMarca;



                    $color = $producto->Color ?? '';

                    $size = $producto->Size ?? '';

                    $name = $producto->Name ?? '';

                    $brand = $producto->Brand ?? '';

                    $stock = $producto->AvailableQuantity ?? 0;



                    $nombre =
                        $brand .
                        ' ' .
                        $name .
                        ' ' .
                        $size .
                        ' ' .
                        $color;



                    $costo = $producto->Price;



                    $precio =
                        number_format(
                            $producto->Price +
                            ($producto->Price * 0.75),
                            2
                        );





                    $SQL =
                    'INSERT INTO producto
                    (
                        nombre,
                        marca,
                        precio,
                        suspendido,
                        stock,
                        estuche,
                        codigo,
                        color,
                        tamano,
                        costo,
                        proveedorExterno,
                        fechaAlta
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';





                    DB::insert(
                        $SQL,
                        [

                            $nombre,

                            $marcaId,

                            $precio,

                            0,

                            $stock,

                            1,

                            $producto->Upc,

                            $color,

                            $size,

                            $costo,

                            'nywd',

                            date('Y-m-d')

                        ]
                    );





                    $idProducto = DB::getPdo()->lastInsertId();






                    $imagenes = json_decode(
                        $producto->Images
                    );



                    $imagenPrincipal = 0;





                    if (!empty($imagenes)) {


                        foreach ($imagenes as $imagen) {



                            DB::insert(
                                'INSERT INTO fotoproducto 
                                (
                                    idProducto,
                                    orden,
                                    url,
                                    descargada
                                )
                                VALUES (?, ?, ?, 1)',
                                [

                                    $idProducto,

                                    $imagen->Number,

                                    $imagen->LargeImageUrl

                                ]
                            );




                            if ($imagenPrincipal == 0) {

                                $imagenPrincipal =
                                    DB::getPdo()->lastInsertId();

                            }


                        }

                    }





                    DB::table('producto')
                    ->where(
                        'id',
                        $idProducto
                    )
                    ->update(
                        [
                            'imagenPrincipal' =>
                                $imagenPrincipal
                        ]
                    );



                }





                $this->logNywd(
                    'Productos nuevos insertados',
                    [
                        'cantidad' => $contadorProductos
                    ]
                );






                DB::update(
                'UPDATE producto

                LEFT JOIN stockExterno 
                    ON stockExterno.Upc = producto.codigo

                LEFT JOIN marcaproducto 
                    ON stockExterno.Brand = marcaproducto.nombre

                SET 

                    producto.stock = stockExterno.availableQuantity,

                    producto.proveedorExterno="nywd",

                    producto.borrado=NULL,

                    producto.marca=marcaproducto.id,

                    producto.ultimoIngresoDeMercaderia=?,

                    producto.costo=stockExterno.price,

                    producto.precio=(stockExterno.price*1.75)

                WHERE stockExterno.Upc IS NOT NULL',
                [
                    date('Y-m-d')
                ]);



                $this->logNywd(
                    'Actualización de stock finalizada'
                );



            });



            return response()->json(

                [
                    'success'=>true,

                    'message'=>'Precios y stock actualizados correctamente.'
                ],

                200

            );





        } catch(Throwable $e) {



            $this->logNywd(
                'ERROR procesando stock NYWD',
                [
                    'error'=>$e->getMessage(),

                    'trace'=>$e->getTraceAsString()
                ]
            );



            Log::error(
                'Error actualizando precios y stock',
                [
                    'error'=>$e->getMessage(),

                    'trace'=>$e->getTraceAsString()
                ]
            );



            return response()->json(

                [
                    'success'=>false,

                    'message'=>'Error al actualizar precios y stock.',

                    'error'=>$e->getMessage()
                ],

                500

            );

        }

    }

    public function insertStock()
    {

        $query = "
        INSERT INTO producto 
        (
            codigo, 
            nombre, 
            marca, 
            imagen
        )

        SELECT 
            t.sku,
            t.nombre,
            t.MarcasVehiculo,
            t.imagen

        FROM stockExterno t

        LEFT JOIN producto p 
            ON t.sku = p.codigo

        WHERE p.codigo IS NULL;
        ";


        try {


            DB::statement($query);



            $this->logNywd(
                'InsertStock ejecutado correctamente'
            );



            return response()->json([
                'message'=>'Productos insertados correctamente'
            ]);



        } catch(Throwable $e) {



            $this->logNywd(
                'ERROR ejecutando insertStock',
                [
                    'error'=>$e->getMessage()
                ]
            );



            throw $e;

        }

    }






    public function getToken()
    {

        try {



            $response = Http::withHeaders([

                'Content-Type'=>'application/json'

            ])->post(
                'https://developer.nywd.com/api/v1/account/login',
                [

                    'username'=>'doralice@mayoristasdeopticas.com',

                    'password'=>':(8U*#}-7wqJwc3e0SVMBuwt^{deeFn){l{7Z%.Uo*F#['

                ]
            );





            if (!$response->successful()) {


                $this->logNywd(
                    'ERROR login NYWD',
                    [
                        'status'=>$response->status(),

                        'respuesta'=>$response->body()
                    ]
                );


                return null;

            }





            $data = $response->json();





            if (!isset($data['AccessToken'])) {


                $this->logNywd(
                    'ERROR respuesta token NYWD sin AccessToken',
                    [
                        'respuesta'=>$data
                    ]
                );


                return null;

            }





            $this->logNywd(
                'Autenticación NYWD correcta'
            );





            return $data['AccessToken'];





        } catch(Throwable $e) {



            $this->logNywd(
                'ERROR generando token NYWD',
                [
                    'error'=>$e->getMessage(),

                    'trace'=>$e->getTraceAsString()
                ]
            );



            return null;

        }

    }

}
