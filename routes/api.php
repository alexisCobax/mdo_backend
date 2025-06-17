<?php

use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use App\Helpers\ProtegerClaveHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JetController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\CloverController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\MonedaController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EstucheController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PagoWebController;
use App\Http\Controllers\PaisWebController;
use App\Http\Controllers\PayeezyController;
use App\Http\Controllers\PortadaController;
use App\Http\Controllers\PreciosController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ZipcodeController;
use App\Http\Controllers\Cliente2Controller;
use App\Http\Controllers\ColorWebController;
use App\Http\Controllers\ComisionController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\OrderjetController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProspectoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReintegroController;
use App\Http\Controllers\CarritoWebController;
use App\Http\Controllers\ClienteWebController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\DescuentosController;
use App\Http\Controllers\InvoiceWebController;
use App\Http\Controllers\PlataformaController;
use App\Http\Controllers\TipobannerController;
use App\Http\Controllers\EtapapedidoController;
use App\Http\Controllers\FormadepagoController;
use App\Http\Controllers\GlobalToolsController;
use App\Http\Controllers\PedidocuponController;
use App\Http\Controllers\ProductoWebController;
use App\Http\Controllers\TipodeenvioController;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\Auth\AuthWebController;
use App\Http\Controllers\EstadopedidoController;
use App\Http\Controllers\FotoproductoController;
use App\Http\Controllers\OrigenpedidoController;
use App\Http\Controllers\SexoproductoController;
use App\Http\Controllers\TipoproductoController;
use App\Http\Controllers\CarriermethodController;
use App\Http\Controllers\CompradetalleController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\MarcaproductoController;
use App\Http\Controllers\PagoManualWebController;
use App\Http\Controllers\PagostarjetumController;
use App\Http\Controllers\PedidodetalleController;
use App\Http\Controllers\ActiveCampaignController;
use App\Http\Controllers\CarritodetalleController;
use App\Http\Controllers\CupondescuentoController;
use App\Http\Controllers\InvoicedetalleController;
use App\Http\Controllers\MarcafalabellaController;
use App\Http\Controllers\ProductogeneroController;
use App\Http\Controllers\TamanoproductoController;
use App\Http\Controllers\ClientecontactoController;
use App\Http\Controllers\CompradetallennController;
use App\Http\Controllers\CotizacionesWebController;
use App\Http\Controllers\CuentaCorrienteController;
use App\Http\Controllers\PedidodetallennController;
use App\Http\Controllers\CotizacionPedidoController;
use App\Http\Controllers\EncargadodeventaController;
use App\Http\Controllers\EstadocotizacionController;
use App\Http\Controllers\MaterialproductoController;
use App\Http\Controllers\PedidoCotizacionController;
use App\Http\Controllers\SubidasfalabellaController;
use App\Http\Controllers\CarritodetalleWebController;
use App\Http\Controllers\CategoriaproductoController;
use App\Http\Controllers\CategoriafalabellaController;
use App\Http\Controllers\MovimientoproductoController;
use App\Http\Controllers\OrderjetdevolucionController;
use App\Http\Controllers\PlataformaproductoController;
use App\Http\Controllers\EmpresatransportadoraController;
use App\Http\Controllers\GoHighLevelController;
use App\Http\Controllers\NotificacionesCotizacionController;
use App\Http\Controllers\NywdController;
use App\Http\Controllers\OrderjetdevoluciondetalleController;
use App\Http\Controllers\PedidodescuentospromocionController;
use App\Http\Controllers\PromocioncomprandoxgratiszController;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'permission:1'])->group(function () {

    /* Banner Routes **/

    Route::get('/banner', [BannerController::class, 'index']);
    Route::get('/banner/{id}', [BannerController::class, 'show']);
    Route::post('/banner', [BannerController::class, 'create']);
    Route::post('/banner/update/{id}', [BannerController::class, 'update']);
    Route::delete('/banner/{id}', [BannerController::class, 'delete']);

    /* Carrier Routes **/

    Route::get('/carrier', [CarrierController::class, 'index']);
    Route::get('/carrier/{id}', [CarrierController::class, 'show']);
    Route::post('/carrier', [CarrierController::class, 'create']);
    Route::put('/carrier/{id}', [CarrierController::class, 'update']);
    Route::delete('/carrier/{id}', [CarrierController::class, 'delete']);

    /* Carriermethod Routes **/

    Route::get('/carriermethod', [CarriermethodController::class, 'index']);
    Route::get('/carriermethod/{id}', [CarriermethodController::class, 'show']);
    Route::post('/carriermethod', [CarriermethodController::class, 'create']);
    Route::put('/carriermethod/{id}', [CarriermethodController::class, 'update']);
    Route::delete('/carriermethod/{id}', [CarriermethodController::class, 'delete']);

    /* Carrito Routes **/

    Route::get('/carrito', [CarritoController::class, 'index']);
    Route::get('/carrito/{id}', [CarritoController::class, 'show']);
    Route::post('/carrito', [CarritoController::class, 'create']);
    Route::put('/carrito/{id}', [CarritoController::class, 'update']);
    Route::delete('/carrito/{id}', [CarritoController::class, 'delete']);
    Route::get('/carrito/status/{id}', [CarritoController::class, 'status']);

    /* Carritodetalle Routes **/

    Route::get('/carritodetalle', [CarritodetalleController::class, 'index']);
    Route::get('/carritodetalle/{id}', [CarritodetalleController::class, 'show']);
    Route::post('/carritodetalle', [CarritodetalleController::class, 'create']);
    Route::put('/carritodetalle/{id}', [CarritodetalleController::class, 'update']);
    Route::delete('/carritodetalle/{id}', [CarritodetalleController::class, 'delete']);

    /* Categoriafalabella Routes **/

    Route::get('/categoriafalabella', [CategoriafalabellaController::class, 'index']);
    Route::get('/categoriafalabella/{id}', [CategoriafalabellaController::class, 'show']);
    Route::post('/categoriafalabella', [CategoriafalabellaController::class, 'create']);
    Route::put('/categoriafalabella/{id}', [CategoriafalabellaController::class, 'update']);
    Route::delete('/categoriafalabella/{id}', [CategoriafalabellaController::class, 'delete']);

    /* Categoriaproducto Routes **/

    // Route::get('/categoriaproducto', [CategoriaproductoController::class, 'index']);
    Route::get('/categoriaproducto/{id}', [CategoriaproductoController::class, 'show']);
    Route::post('/categoriaproducto', [CategoriaproductoController::class, 'create']);
    Route::put('/categoriaproducto/{id}', [CategoriaproductoController::class, 'update']);
    Route::delete('/categoriaproducto/{id}', [CategoriaproductoController::class, 'delete']);

    /* Ciudad Routes **/

    Route::get('/ciudad', [CiudadController::class, 'index']);
    Route::get('/ciudad/{id}', [CiudadController::class, 'show']);
    Route::post('/ciudad', [CiudadController::class, 'create']);
    Route::put('/ciudad/{id}', [CiudadController::class, 'update']);
    Route::delete('/ciudad/{id}', [CiudadController::class, 'delete']);

    /* Cliente Routes **/

    Route::get('/cliente', [ClienteController::class, 'index']);
    Route::get('/cliente/{id}', [ClienteController::class, 'show']);
    Route::post('/cliente', [ClienteController::class, 'create']);
    Route::put('/cliente/{id}', [ClienteController::class, 'update']);
    Route::delete('/cliente/{id}', [ClienteController::class, 'delete']);

    /* Cliente2 Routes **/

    Route::get('/cliente2', [Cliente2Controller::class, 'index']);
    Route::get('/cliente2/{id}', [Cliente2Controller::class, 'show']);
    Route::post('/cliente2', [Cliente2Controller::class, 'create']);
    Route::put('/cliente2/{id}', [Cliente2Controller::class, 'update']);
    Route::delete('/cliente2/{id}', [Cliente2Controller::class, 'delete']);

    /* Clientecontacto Routes **/

    Route::get('/clientecontacto', [ClientecontactoController::class, 'index']);
    Route::get('/clientecontacto/{id}', [ClientecontactoController::class, 'show']);
    Route::post('/clientecontacto', [ClientecontactoController::class, 'create']);
    Route::put('/clientecontacto/{id}', [ClientecontactoController::class, 'update']);
    Route::delete('/clientecontacto/{id}', [ClientecontactoController::class, 'delete']);

    /* Color Routes **/

    Route::get('/color', [ColorController::class, 'index']);
    Route::get('/color/{id}', [ColorController::class, 'show']);
    Route::post('/color', [ColorController::class, 'create']);
    Route::put('/color/{id}', [ColorController::class, 'update']);
    Route::delete('/color/{id}', [ColorController::class, 'delete']);

    /* Comision Routes **/

    Route::get('/comision', [ComisionController::class, 'index']);
    Route::get('/comision/{id}', [ComisionController::class, 'show']);
    Route::post('/comision', [ComisionController::class, 'create']);
    Route::put('/comision/{id}', [ComisionController::class, 'update']);
    Route::delete('/comision/{id}', [ComisionController::class, 'delete']);

    /* Compra Routes **/

    Route::get('/compra', [CompraController::class, 'index']);
    Route::get('/compra/{id}', [CompraController::class, 'show']);
    Route::post('/compra', [CompraController::class, 'create']);
    Route::put('/compra/{id}', [CompraController::class, 'update']);
    Route::delete('/compra/{id}', [CompraController::class, 'delete']);

    /* Compradetalle Routes **/

    Route::get('/compradetalle', [CompradetalleController::class, 'index']);
    Route::get('/compradetalle/{id}', [CompradetalleController::class, 'show']);
    Route::post('/compradetalle', [CompradetalleController::class, 'create']);
    Route::put('/compradetalle/{id}', [CompradetalleController::class, 'update']);
    Route::delete('/compradetalle/{id}', [CompradetalleController::class, 'delete']);

    /* Compradetallenn Routes **/

    Route::get('/compradetallenn', [CompradetallennController::class, 'index']);
    Route::get('/compradetallenn/{id}', [CompradetallennController::class, 'show']);
    Route::post('/compradetallenn', [CompradetallennController::class, 'create']);
    Route::put('/compradetallenn/{id}', [CompradetallennController::class, 'update']);
    Route::delete('/compradetallenn/{id}', [CompradetallennController::class, 'delete']);

    /* Configuracion Routes **/

    Route::get('/configuracion', [ConfiguracionController::class, 'index']);
    Route::get('/configuracion/{id}', [ConfiguracionController::class, 'show']);
    Route::post('/configuracion', [ConfiguracionController::class, 'create']);
    Route::put('/configuracion/{id}', [ConfiguracionController::class, 'update']);
    Route::delete('/configuracion/{id}', [ConfiguracionController::class, 'delete']);

    // /** Cotizacion Routes **/

    // Route::get('/cotizacion', [CotizacionController::class, 'index']);
    // Route::get('/cotizacion/{id}', [CotizacionController::class, 'show']);
    // Route::post('/cotizacion', [CotizacionController::class, 'create']);
    // Route::put('/cotizacion/{id}', [CotizacionController::class, 'update']);
    // Route::delete('/cotizacion/{id}', [CotizacionController::class, 'delete']);

    // /** Cotizaciondetalle Routes **/

    // Route::get('/cotizaciondetalle', [CotizaciondetalleController::class, 'index']);
    // Route::get('/cotizaciondetalle/{id}', [CotizaciondetalleController::class, 'show']);
    // Route::post('/cotizaciondetalle', [CotizaciondetalleController::class, 'create']);
    // Route::put('/cotizaciondetalle/{id}', [CotizaciondetalleController::class, 'update']);
    // Route::delete('/cotizaciondetalle/{id}', [CotizaciondetalleController::class, 'delete']);

    Route::get('/cotizacion', [CotizacionController::class, 'index']);
    Route::get('/cotizacion/{id}', [CotizacionController::class, 'show']);
    Route::post('/cotizacion', [CotizacionController::class, 'create']);
    Route::put('/cotizacion/{id}', [CotizacionController::class, 'update']);
    Route::delete('/cotizacion/{id}', [CotizacionController::class, 'delete']);

    /* CotizacionPedido - PedidoCotizacionRoutes **/

    Route::post('/cotizacion/pedido', [CotizacionPedidoController::class, 'create']);
    Route::post('/pedido/cotizacion', [PedidoCotizacionController::class, 'create']);

    /* Cupondescuento Routes **/

    Route::get('/cupondescuento', [CupondescuentoController::class, 'index']);
    Route::get('/cupondescuento/{id}', [CupondescuentoController::class, 'show']);
    Route::post('/cupondescuento', [CupondescuentoController::class, 'create']);
    Route::put('/cupondescuento/{id}', [CupondescuentoController::class, 'update']);
    Route::delete('/cupondescuento/{id}', [CupondescuentoController::class, 'delete']);

    /* CuentaCorriente Routes **/

    Route::get('/cuentacorriente/{id}', [CuentaCorrienteController::class, 'show']);

    /* Deposito Routes **/

    Route::get('/deposito', [DepositoController::class, 'index']);
    Route::get('/deposito/{id}', [DepositoController::class, 'show']);
    Route::post('/deposito', [DepositoController::class, 'create']);
    Route::put('/deposito/{id}', [DepositoController::class, 'update']);
    Route::delete('/deposito/{id}', [DepositoController::class, 'delete']);
    Route::post('/deposito/ingreso', [DepositoController::class, 'ingreso']);

    /* Descuentos **/

    Route::post('/descuentos', [DescuentosController::class, 'index']);
    Route::post('/descuentos/generales', [DescuentosController::class, 'create']);

    /* Empleado Routes **/

    Route::get('/empleado', [EmpleadoController::class, 'index']);
    Route::get('/empleado/{id}', [EmpleadoController::class, 'show']);
    Route::post('/empleado', [EmpleadoController::class, 'create']);
    Route::put('/empleado/{id}', [EmpleadoController::class, 'update']);
    Route::delete('/empleado/{id}', [EmpleadoController::class, 'delete']);

    /* Empresatransportadora Routes **/

    Route::get('/empresatransportadora', [EmpresatransportadoraController::class, 'index']);
    Route::get('/empresatransportadora/{id}', [EmpresatransportadoraController::class, 'show']);
    Route::post('/empresatransportadora', [EmpresatransportadoraController::class, 'create']);
    Route::put('/empresatransportadora/{id}', [EmpresatransportadoraController::class, 'update']);
    Route::delete('/empresatransportadora/{id}', [EmpresatransportadoraController::class, 'delete']);

    /* Encargadodeventa Routes **/

    Route::get('/encargadodeventa', [EncargadodeventaController::class, 'index']);
    Route::get('/encargadodeventa/{id}', [EncargadodeventaController::class, 'show']);
    Route::post('/encargadodeventa', [EncargadodeventaController::class, 'create']);
    Route::put('/encargadodeventa/{id}', [EncargadodeventaController::class, 'update']);
    Route::delete('/encargadodeventa/{id}', [EncargadodeventaController::class, 'delete']);

    /* Estadocotizacion Routes **/

    Route::get('/estadocotizacion', [EstadocotizacionController::class, 'index']);
    Route::get('/estadocotizacion/{id}', [EstadocotizacionController::class, 'show']);
    Route::post('/estadocotizacion', [EstadocotizacionController::class, 'create']);
    Route::put('/estadocotizacion/{id}', [EstadocotizacionController::class, 'update']);
    Route::delete('/estadocotizacion/{id}', [EstadocotizacionController::class, 'delete']);

    /* Estadopedido Routes **/

    Route::get('/estadopedido', [EstadopedidoController::class, 'index']);
    Route::get('/estadopedido/{id}', [EstadopedidoController::class, 'show']);
    Route::post('/estadopedido', [EstadopedidoController::class, 'create']);
    Route::put('/estadopedido/{id}', [EstadopedidoController::class, 'update']);
    Route::delete('/estadopedido/{id}', [EstadopedidoController::class, 'delete']);

    /* Estuche Routes **/

    Route::get('/estuche', [EstucheController::class, 'index']);
    Route::get('/estuche/{id}', [EstucheController::class, 'show']);
    Route::post('/estuche', [EstucheController::class, 'create']);
    Route::put('/estuche/{id}', [EstucheController::class, 'update']);
    Route::delete('/estuche/{id}', [EstucheController::class, 'delete']);

    /* Etapapedido Routes **/

    Route::get('/etapapedido', [EtapapedidoController::class, 'index']);
    Route::get('/etapapedido/{id}', [EtapapedidoController::class, 'show']);
    Route::post('/etapapedido', [EtapapedidoController::class, 'create']);
    Route::put('/etapapedido/{id}', [EtapapedidoController::class, 'update']);
    Route::delete('/etapapedido/{id}', [EtapapedidoController::class, 'delete']);

    /* Formadepago Routes **/

    Route::get('/formadepago', [FormadepagoController::class, 'index']);
    Route::get('/formadepago/{id}', [FormadepagoController::class, 'show']);
    Route::post('/formadepago', [FormadepagoController::class, 'create']);
    Route::put('/formadepago/{id}', [FormadepagoController::class, 'update']);
    Route::delete('/formadepago/{id}', [FormadepagoController::class, 'delete']);

    /* Fotoproducto Routes **/

    Route::get('/fotoproducto', [FotoproductoController::class, 'index']);
    Route::get('/fotoproducto/{id}', [FotoproductoController::class, 'show']);
    Route::post('/fotoproducto', [FotoproductoController::class, 'create']);
    Route::put('/fotoproducto/{id}', [FotoproductoController::class, 'update']);
    Route::delete('/fotoproducto/{id}', [FotoproductoController::class, 'delete']);

    /* Grupo Routes **/

    Route::get('/grupo', [GrupoController::class, 'index']);
    Route::post('/grupo', [GrupoController::class, 'create']);
    Route::put('/grupo/{id}', [GrupoController::class, 'update']);
    Route::delete('/grupo/{id}', [GrupoController::class, 'delete']);

    /* Globals Routes **/

    Route::get('/global', [GlobalToolsController::class, 'index']);

    /* Invoice Routes **/

    Route::get('/invoice', [InvoiceController::class, 'index']);
    Route::get('/invoice/{id}', [InvoiceController::class, 'show']);
    Route::post('/invoice', [InvoiceController::class, 'create']);
    Route::put('/invoice/regenerar/{id}', [InvoiceController::class, 'regenerate']);
    Route::put('/invoice/{id}', [InvoiceController::class, 'update']);
    Route::put('/invoice/update/send/{id}', [InvoiceController::class, 'updateSend']);
    Route::delete('/invoice/{id}', [InvoiceController::class, 'delete']);

    /* Invoicedetalle Routes **/

    Route::get('/invoicedetalle', [InvoicedetalleController::class, 'index']);
    Route::get('/invoicedetalle/{id}', [InvoicedetalleController::class, 'show']);
    Route::post('/invoicedetalle', [InvoicedetalleController::class, 'create']);
    Route::put('/invoicedetalle/{id}', [InvoicedetalleController::class, 'update']);
    Route::delete('/invoicedetalle/{id}', [InvoicedetalleController::class, 'delete']);

    /* Jet Routes **/

    Route::get('/jet', [JetController::class, 'index']);
    Route::get('/jet/{id}', [JetController::class, 'show']);
    Route::post('/jet', [JetController::class, 'create']);
    Route::put('/jet/{id}', [JetController::class, 'update']);
    Route::delete('/jet/{id}', [JetController::class, 'delete']);

    /* Marcafalabella Routes **/

    Route::get('/marcafalabella', [MarcafalabellaController::class, 'index']);
    Route::get('/marcafalabella/{id}', [MarcafalabellaController::class, 'show']);
    Route::post('/marcafalabella', [MarcafalabellaController::class, 'create']);
    Route::put('/marcafalabella/{id}', [MarcafalabellaController::class, 'update']);
    Route::delete('/marcafalabella/{id}', [MarcafalabellaController::class, 'delete']);

    /* Marcaproducto Routes **/

    Route::get('/marcaproducto', [MarcaproductoController::class, 'index']);
    Route::get('/marcaproducto/{id}', [MarcaproductoController::class, 'show']);
    Route::post('/marcaproducto', [MarcaproductoController::class, 'create']);
    Route::put('/marcaproducto/{id}', [MarcaproductoController::class, 'update']);
    Route::delete('/marcaproducto/{id}', [MarcaproductoController::class, 'delete']);

    /* Materialproducto Routes **/

    Route::get('/materialproducto', [MaterialproductoController::class, 'index']);
    Route::get('/materialproducto/{id}', [MaterialproductoController::class, 'show']);
    Route::post('/materialproducto', [MaterialproductoController::class, 'create']);
    Route::put('/materialproducto/{id}', [MaterialproductoController::class, 'update']);
    Route::delete('/materialproducto/{id}', [MaterialproductoController::class, 'delete']);

    /* Moneda Routes **/

    Route::get('/moneda', [MonedaController::class, 'index']);
    Route::get('/moneda/{id}', [MonedaController::class, 'show']);
    Route::post('/moneda', [MonedaController::class, 'create']);
    Route::put('/moneda/{id}', [MonedaController::class, 'update']);
    Route::delete('/moneda/{id}', [MonedaController::class, 'delete']);

    /* Movimientoproducto Routes **/

    Route::get('/movimientoproducto', [MovimientoproductoController::class, 'index']);
    Route::get('/movimientoproducto/{id}', [MovimientoproductoController::class, 'show']);
    Route::post('/movimientoproducto', [MovimientoproductoController::class, 'create']);
    Route::put('/movimientoproducto/{id}', [MovimientoproductoController::class, 'update']);
    Route::delete('/movimientoproducto/{id}', [MovimientoproductoController::class, 'delete']);

    /* Orderjet Routes **/

    Route::get('/orderjet', [OrderjetController::class, 'index']);
    Route::get('/orderjet/{id}', [OrderjetController::class, 'show']);
    Route::post('/orderjet', [OrderjetController::class, 'create']);
    Route::put('/orderjet/{id}', [OrderjetController::class, 'update']);
    Route::delete('/orderjet/{id}', [OrderjetController::class, 'delete']);

    /* Orderjetdevolucion Routes **/

    Route::get('/orderjetdevolucion', [OrderjetdevolucionController::class, 'index']);
    Route::get('/orderjetdevolucion/{id}', [OrderjetdevolucionController::class, 'show']);
    Route::post('/orderjetdevolucion', [OrderjetdevolucionController::class, 'create']);
    Route::put('/orderjetdevolucion/{id}', [OrderjetdevolucionController::class, 'update']);
    Route::delete('/orderjetdevolucion/{id}', [OrderjetdevolucionController::class, 'delete']);

    /* Orderjetdevoluciondetalle Routes **/

    Route::get('/orderjetdevoluciondetalle', [OrderjetdevoluciondetalleController::class, 'index']);
    Route::get('/orderjetdevoluciondetalle/{id}', [OrderjetdevoluciondetalleController::class, 'show']);
    Route::post('/orderjetdevoluciondetalle', [OrderjetdevoluciondetalleController::class, 'create']);
    Route::put('/orderjetdevoluciondetalle/{id}', [OrderjetdevoluciondetalleController::class, 'update']);
    Route::delete('/orderjetdevoluciondetalle/{id}', [OrderjetdevoluciondetalleController::class, 'delete']);

    /* Origenpedido Routes **/

    Route::get('/origenpedido', [OrigenpedidoController::class, 'index']);
    Route::get('/origenpedido/{id}', [OrigenpedidoController::class, 'show']);
    Route::post('/origenpedido', [OrigenpedidoController::class, 'create']);
    Route::put('/origenpedido/{id}', [OrigenpedidoController::class, 'update']);
    Route::delete('/origenpedido/{id}', [OrigenpedidoController::class, 'delete']);

    /* Pagostarjetum Routes **/

    Route::get('/pagostarjetum', [PagostarjetumController::class, 'index']);
    Route::get('/pagostarjetum/{id}', [PagostarjetumController::class, 'show']);
    Route::post('/pagostarjetum', [PagostarjetumController::class, 'create']);
    Route::put('/pagostarjetum/{id}', [PagostarjetumController::class, 'update']);
    Route::delete('/pagostarjetum/{id}', [PagostarjetumController::class, 'delete']);

    /* Pais Routes **/

    Route::get('/pais', [PaisController::class, 'index']);
    Route::get('/pais/{id}', [PaisController::class, 'show']);
    Route::post('/pais', [PaisController::class, 'create']);
    Route::put('/pais/{id}', [PaisController::class, 'update']);
    Route::delete('/pais/{id}', [PaisController::class, 'delete']);

    /* Paypal Routes **/

    Route::get('/paypal', [PaypalController::class, 'index']);
    Route::get('/paypal/{id}', [PaypalController::class, 'show']);
    Route::post('/paypal', [PaypalController::class, 'create']);
    Route::put('/paypal/{id}', [PaypalController::class, 'update']);
    Route::delete('/paypal/{id}', [PaypalController::class, 'delete']);

    /* Pedido Routes **/

    Route::get('/pedido', [PedidoController::class, 'index']);
    Route::get('/pedido/{id}', [PedidoController::class, 'show']);
    Route::post('/pedido', [PedidoController::class, 'create']);
    Route::post('/pedido/nuevo', [PedidoController::class, 'createNuevo']);
    Route::put('/pedido/{id}', [PedidoController::class, 'update']);
    Route::delete('/pedido/{id}', [PedidoController::class, 'delete']);

    /* Pedidocupon Routes **/

    Route::get('/pedidocupon', [PedidocuponController::class, 'index']);
    Route::get('/pedidocupon/{id}', [PedidocuponController::class, 'show']);
    Route::post('/pedidocupon', [PedidocuponController::class, 'create']);
    Route::put('/pedidocupon/{id}', [PedidocuponController::class, 'update']);
    Route::delete('/pedidocupon/{id}', [PedidocuponController::class, 'delete']);

    /* Pedidodescuentospromocion Routes **/

    Route::get('/pedidodescuentospromocion', [PedidodescuentospromocionController::class, 'index']);
    Route::get('/pedidodescuentospromocion/{id}', [PedidodescuentospromocionController::class, 'show']);
    Route::post('/pedidodescuentospromocion', [PedidodescuentospromocionController::class, 'create']);
    Route::put('/pedidodescuentospromocion/{id}', [PedidodescuentospromocionController::class, 'update']);
    Route::delete('/pedidodescuentospromocion/{id}', [PedidodescuentospromocionController::class, 'delete']);

    /* Pedidodetalle Routes **/

    Route::get('/pedidodetalle', [PedidodetalleController::class, 'index']);
    Route::get('/pedidodetalle/{id}', [PedidodetalleController::class, 'show']);
    Route::get('/pedidodetalle/pedido/{id}', [PedidodetalleController::class, 'showDetalle']);
    Route::post('/pedidodetalle', [PedidodetalleController::class, 'create']);

    Route::put('/pedidodetalle/{id}', [PedidodetalleController::class, 'update']);

    Route::put('/pedidodetalle/producto/{id}', [PedidodetalleController::class, 'updateProducto']);

    Route::delete('/pedidodetalle/{id}', [PedidodetalleController::class, 'delete']);
    Route::delete('/pedidodetalle/producto/{id}', [PedidodetalleController::class, 'deleteProducto']);

    /* Pedidodetallenn Routes **/

    Route::get('/pedidodetallenn', [PedidodetallennController::class, 'index']);
    Route::get('/pedidodetallenn/{id}', [PedidodetallennController::class, 'show']);
    Route::get('/pedidodetallenn/pedido/{id}', [PedidodetallennController::class, 'showPedido']);
    Route::post('/pedidodetallenn', [PedidodetallennController::class, 'create']);
    Route::put('/pedidodetallenn/{id}', [PedidodetallennController::class, 'update']);
    Route::delete('/pedidodetallenn/{id}', [PedidodetallennController::class, 'delete']);

    /* Perfil Routes **/

    Route::get('/perfil', [PerfilController::class, 'index']);
    Route::get('/perfil/{id}', [PerfilController::class, 'show']);
    Route::post('/perfil', [PerfilController::class, 'create']);
    Route::put('/perfil/{id}', [PerfilController::class, 'update']);
    Route::delete('/perfil/{id}', [PerfilController::class, 'delete']);

    /* Plataforma Routes **/

    Route::get('/plataforma', [PlataformaController::class, 'index']);
    Route::get('/plataforma/{id}', [PlataformaController::class, 'show']);
    Route::post('/plataforma', [PlataformaController::class, 'create']);
    Route::put('/plataforma/{id}', [PlataformaController::class, 'update']);
    Route::delete('/plataforma/{id}', [PlataformaController::class, 'delete']);

    /* Plataformaproducto Routes **/

    Route::get('/plataformaproducto', [PlataformaproductoController::class, 'index']);
    Route::get('/plataformaproducto/{id}', [PlataformaproductoController::class, 'show']);
    Route::post('/plataformaproducto', [PlataformaproductoController::class, 'create']);
    Route::put('/plataformaproducto/{id}', [PlataformaproductoController::class, 'update']);
    Route::delete('/plataformaproducto/{id}', [PlataformaproductoController::class, 'delete']);

    /* Portada Routes **/

    Route::get('/portada', [PortadaController::class, 'index']);
    Route::get('/portada/{id}', [PortadaController::class, 'show']);
    Route::post('/portada', [PortadaController::class, 'create']);
    Route::put('/portada/{id}', [PortadaController::class, 'update']);
    Route::delete('/portada/{id}', [PortadaController::class, 'delete']);

    /* Producto Routes **/

    Route::get('/producto', [ProductoController::class, 'index']);
    Route::get('/producto/{id}', [ProductoController::class, 'show']);
    Route::get('/producto/codigo/{codigo}', [ProductoController::class, 'showCodigo']);
    Route::get('/producto/stock/{id}', [ProductoController::class, 'stock']);
    Route::post('/producto', [ProductoController::class, 'create']);
    Route::put('/producto/{id}', [ProductoController::class, 'update']);
    Route::put('/producto/precios/general', [ProductoController::class, 'precioGeneral']);
    Route::delete('/producto/{id}', [ProductoController::class, 'delete']);
    // Route::post('/producto/related', [ProductoController::class, 'related']);

    /* Productogenero Routes **/

    Route::get('/productogenero', [ProductogeneroController::class, 'index']);
    Route::get('/productogenero/{id}', [ProductogeneroController::class, 'show']);
    Route::post('/productogenero', [ProductogeneroController::class, 'create']);
    Route::put('/productogenero/{id}', [ProductogeneroController::class, 'update']);
    Route::delete('/productogenero/{id}', [ProductogeneroController::class, 'delete']);

    /* Promocioncomprandoxgratisz Routes **/

    Route::get('/promocioncomprandoxgratisz', [PromocioncomprandoxgratiszController::class, 'index']);
    Route::get('/promocioncomprandoxgratisz/{id}', [PromocioncomprandoxgratiszController::class, 'show']);
    Route::post('/promocioncomprandoxgratisz', [PromocioncomprandoxgratiszController::class, 'create']);
    Route::put('/promocioncomprandoxgratisz/{id}', [PromocioncomprandoxgratiszController::class, 'update']);
    Route::delete('/promocioncomprandoxgratisz/{id}', [PromocioncomprandoxgratiszController::class, 'delete']);

    /* Prospecto Routes **/

    Route::get('/prospecto', [ProspectoController::class, 'index']);
    Route::get('/prospecto/{id}', [ProspectoController::class, 'show']);
    Route::post('/prospecto', [ProspectoController::class, 'create']);
    Route::put('/prospecto/{id}', [ProspectoController::class, 'update']);
    Route::delete('/prospecto/{id}', [ProspectoController::class, 'delete']);

    /* Proveedor Routes **/

    Route::get('/proveedor', [ProveedorController::class, 'index']);
    Route::get('/proveedor/{id}', [ProveedorController::class, 'show']);
    Route::post('/proveedor', [ProveedorController::class, 'create']);
    Route::put('/proveedor/{id}', [ProveedorController::class, 'update']);
    Route::delete('/proveedor/{id}', [ProveedorController::class, 'delete']);

    /* Puesto Routes **/

    Route::get('/puesto', [PuestoController::class, 'index']);
    Route::get('/puesto/{id}', [PuestoController::class, 'show']);
    Route::post('/puesto', [PuestoController::class, 'create']);
    Route::put('/puesto/{id}', [PuestoController::class, 'update']);
    Route::delete('/puesto/{id}', [PuestoController::class, 'delete']);

    /* Excel To Json Route**/
    Route::post('/procesar-excel', [ExcelController::class, 'procesarExcel']);

    /* Recibo Routes **/

    Route::get('/recibo', [ReciboController::class, 'index']);
    Route::get('/recibo/{id}', [ReciboController::class, 'show']);
    Route::post('/recibo', [ReciboController::class, 'create']);
    Route::post('/recibo/manual', [ReciboController::class, 'createOne']);
    Route::put('/recibo/{id}', [ReciboController::class, 'update']);
    Route::delete('/recibo/{id}', [ReciboController::class, 'delete']);

    /* Reintegro Routes **/

    Route::get('/reintegro', [ReintegroController::class, 'index']);
    Route::get('/reintegro/{id}', [ReintegroController::class, 'show']);
    Route::post('/reintegro', [ReintegroController::class, 'create']);
    Route::put('/reintegro/{id}', [ReintegroController::class, 'update']);
    Route::delete('/reintegro/{id}', [ReintegroController::class, 'delete']);

    /* Sesion Routes **/

    Route::get('/sesion', [SesionController::class, 'index']);
    Route::get('/sesion/{id}', [SesionController::class, 'show']);
    Route::post('/sesion', [SesionController::class, 'create']);
    Route::put('/sesion/{id}', [SesionController::class, 'update']);
    Route::delete('/sesion/{id}', [SesionController::class, 'delete']);

    /* Sexoproducto Routes **/

    Route::get('/sexoproducto', [SexoproductoController::class, 'index']);
    Route::get('/sexoproducto/{id}', [SexoproductoController::class, 'show']);
    Route::post('/sexoproducto', [SexoproductoController::class, 'create']);
    Route::put('/sexoproducto/{id}', [SexoproductoController::class, 'update']);
    Route::delete('/sexoproducto/{id}', [SexoproductoController::class, 'delete']);

    /* Subidasfalabella Routes **/

    Route::get('/subidasfalabella', [SubidasfalabellaController::class, 'index']);
    Route::get('/subidasfalabella/{id}', [SubidasfalabellaController::class, 'show']);
    Route::post('/subidasfalabella', [SubidasfalabellaController::class, 'create']);
    Route::put('/subidasfalabella/{id}', [SubidasfalabellaController::class, 'update']);
    Route::delete('/subidasfalabella/{id}', [SubidasfalabellaController::class, 'delete']);

    /* Tamanoproducto Routes **/

    Route::get('/tamanoproducto', [TamanoproductoController::class, 'index']);
    Route::get('/tamanoproducto/{id}', [TamanoproductoController::class, 'show']);
    Route::post('/tamanoproducto', [TamanoproductoController::class, 'create']);
    Route::put('/tamanoproducto/{id}', [TamanoproductoController::class, 'update']);
    Route::delete('/tamanoproducto/{id}', [TamanoproductoController::class, 'delete']);

    /* Tipobanner Routes **/

    Route::get('/tipobanner/{id}', [TipobannerController::class, 'show']);
    Route::post('/tipobanner', [TipobannerController::class, 'create']);
    Route::put('/tipobanner/{id}', [TipobannerController::class, 'update']);
    Route::delete('/tipobanner/{id}', [TipobannerController::class, 'delete']);

    /* Tipodeenvio Routes **/

    Route::get('/tipodeenvio', [TipodeenvioController::class, 'index']);
    Route::get('/tipodeenvio/{id}', [TipodeenvioController::class, 'show']);
    Route::post('/tipodeenvio', [TipodeenvioController::class, 'create']);
    Route::put('/tipodeenvio/{id}', [TipodeenvioController::class, 'update']);
    Route::delete('/tipodeenvio/{id}', [TipodeenvioController::class, 'delete']);

    /* Tipoproducto Routes **/

    Route::get('/tipoproducto', [TipoproductoController::class, 'index']);
    Route::get('/tipoproducto/{id}', [TipoproductoController::class, 'show']);
    Route::post('/tipoproducto', [TipoproductoController::class, 'create']);
    Route::put('/tipoproducto/{id}', [TipoproductoController::class, 'update']);
    Route::delete('/tipoproducto/{id}', [TipoproductoController::class, 'delete']);

    /* Transaccion Routes **/

    Route::get('/transaccion', [TransaccionController::class, 'index']);
    Route::get('/transaccion/{id}', [TransaccionController::class, 'show']);
    Route::post('/transaccion', [TransaccionController::class, 'create']);
    Route::put('/transaccion/{id}', [TransaccionController::class, 'update']);
    Route::delete('/transaccion/{id}', [TransaccionController::class, 'delete']);

    /* Usuario Routes **/

    Route::get('/usuario', [UsuarioController::class, 'index']);
    Route::get('/usuario/{id}', [UsuarioController::class, 'show']);
    Route::post('/usuario', [UsuarioController::class, 'create']);
    Route::put('/usuario/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuario/{id}', [UsuarioController::class, 'delete']);

    /* Zipcode Routes **/

    Route::get('/zipcode', [ZipcodeController::class, 'index']);
    Route::get('/zipcode/{id}', [ZipcodeController::class, 'show']);
    Route::post('/zipcode', [ZipcodeController::class, 'create']);
    Route::put('/zipcode/{id}', [ZipcodeController::class, 'update']);
    Route::delete('/zipcode/{id}', [ZipcodeController::class, 'delete']);

    /* Tools Routes **/

    Route::post('/excel', [ExcelController::class, 'index']);

    /* Upload Imagenes **/

    Route::post('/upload/images', [ImageController::class, 'upload'])->name('upload.images');

    /* Login Routes **/

    Route::post('me', [AuthController::class, 'me']);

    /**Variantes de precios **/

    Route::post('/precios/manejador', [PreciosController::class, 'create']);
});

Route::middleware(['auth:sanctum', 'permission:2'])->group(function () {

    /*
     *
     * WEB Routes
     *
     **/

    /* Carrito**/
    Route::post('/web/carrito/status', [CarritoWebController::class, 'show']);
    Route::post('/web/carrito/cotizacion', [CarritoWebController::class, 'procesar']);
    Route::post('/web/refresh', [AuthWebController::class, 'refresh']);

    /* Invoice **/
    Route::post('/web/invoice', [InvoiceWebController::class, 'index']);

    /* Cotizaciones **/
    Route::post('/web/cotizaciones', [CotizacionesWebController::class, 'index']);
    Route::post('/web/cotizacion/carrito', [CotizacionesWebController::class, 'procesar']);

    /* usuario **/
    Route::post('/web/usuario/password', [AuthWebController::class, 'change']);

    /* Carrito Detalle**/
    Route::get('/web/carritodetalle', [CarritodetalleWebController::class, 'show']);
    Route::post('/web/carritodetalle', [CarritodetalleWebController::class, 'create']);
    Route::put('/web/carritodetalle/{id}', [CarritodetalleWebController::class, 'update']);
    Route::delete('/web/carritodetalle/{id}', [CarritodetalleWebController::class, 'delete']);

    /* Cliente **/

    Route::put('/web/cliente', [ClienteWebController::class, 'update']);
    Route::get('/web/cliente', [ClienteWebController::class, 'show']);

    /* Pagos **/
    Route::post('/web/pagar/carrito', [PagoWebController::class, 'create']);
    Route::post('/web/pagar/manual', [PagoManualWebController::class, 'create']);

    /* Descuentos **/
    Route::post('/web/descuento', [DescuentosController::class, 'show']);
    Route::post('/web/descuento/add', [DescuentosController::class, 'add']);

    Route::post('/web/me', [AuthWebController::class, 'me']);
});

/* Producto ESTO DEBE IR SIN TOKEN**/
Route::get('/tipobanner', [TipobannerController::class, 'index']);
Route::get('/web/banner/{tag}', [BannerController::class, 'tag']);
Route::get('/web/color', [ColorWebController::class, 'index']);
Route::get('/web/producto/{id}', [ProductoWebController::class, 'show']);
Route::get('/web/producto', [ProductoWebController::class, 'index']);
Route::post('/producto/related', [ProductoController::class, 'related']);
Route::get('/web/marcaproducto', [MarcaproductoController::class, 'index']);
Route::get('/web/vistamarca', [MarcaproductoController::class, 'vista']);
Route::get('/web/generar-productos-csv', [ExcelController::class, 'GenerarProductosCsv']);

/* Login Routes Not Auth **/
Route::post('/web/login', [AuthWebController::class, 'login']);
Route::post('/web/logout', [AuthWebController::class, 'logout']);
Route::post('/web/register', [AuthWebController::class, 'register']);
Route::post('/web/rescue', [AuthWebController::class, 'rescue']);

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']);

/* Categorias Routes Not Auth **/

Route::get('/categoriaproducto', [CategoriaproductoController::class, 'index']);

/* Grupo Routes Not Auth **/

Route::get('/grupo', [GrupoController::class, 'index']);
Route::get('/grupo/{id}', [GrupoController::class, 'show']);

/* Producto Routes Not Auth **/

// Route::get('/producto', [ProductoController::class, 'index']);
// Route::get('/producto/{id}', [ProductoController::class, 'show']);

Route::get('/pdf/proforma/{id}', [PdfController::class, 'proforma']);

Route::get('/pdf/invoice/{id}', [PdfController::class, 'invoice']);

Route::get('/pdf/recibo/{id}', [PdfController::class, 'recibo']);

Route::get('/pdf/cotizacion/{id}', [PdfController::class, 'cotizacion']);

Route::post('/payment/payeezy', [PayeezyController::class, 'processPayeezyPayment']);

Route::post('/payment/clover', [CloverController::class, 'processCloverPayment']);

Route::post('/subir-cuenta', [ActiveCampaignController::class, 'subirCuenta']);

Route::post('/subir-contacto', [ActiveCampaignController::class, 'subirContacto']);

Route::get('/web/pais', [PaisWebController::class, 'index']);

Route::post('/web/cliente', [ClienteWebController::class, 'create']);

Route::get('/web/cliente/buscar', [ClienteWebController::class, 'find']);

Route::get('excel/prospecto', [ExcelController::class, 'prospectoExcel']);

Route::get('excel/cliente', [ExcelController::class, 'clienteExcel']);

Route::get('excel/cliente/cotizacion/{id}', [ExcelController::class, 'clienteExcel']);

Route::get('/cotizacion/excel/{id}', [CotizacionController::class, 'excel']);

/* Notificacion cron **/

Route::get('notificacion/cotizacion', [NotificacionesCotizacionController::class, 'cotizacion']);

// Route::post('test', function(Request $request){
//     // Verificar si el usuario actual es un administrador
//     //if (Auth::user()->is_admin) {
//         // Buscar el usuario al que se quiere impersonar
//         $user = Usuario::where('nombre', $request->nombre)->first();
//         // Si el usuario existe
//         if ($user) {
//             // Iniciar sesión como el usuario
//             Auth::login($user);

//             // Crear un token de Sanctum para el usuario
//             $token = $user->createToken('impersonation');

//             // Devolver el token al cliente
//             return response()->json([
//                 'token' => $token->plainTextToken
//             ]);
//         } else {
//             return response()->json(['error' => 'User not found'], 404);
//         }
//     // } else {
//     //     return response()->json(['error' => 'Unauthorized'], 403);
//     // }
// });

    /* Reportes **/
    Route::get('/reportes/stock/report', [ReportesController::class, 'stockReport']);
    Route::get('/reportes/stock/list', [ReportesController::class, 'stockList']);
    Route::get('/reportes/productos/report', [ReportesController::class, 'productosReport']);
    Route::get('/reportes/productos/list', [ReportesController::class, 'productosList']);
    Route::get('/reportes/invoices/report', [ReportesController::class, 'invoicesReport']);
    Route::get('/reportes/invoices/list', [ReportesController::class, 'invoicesList']);
    Route::get('/reportes/clientes/report', [ReportesController::class, 'topClientesReport']);
    Route::get('/reportes/clientes/list', [ReportesController::class, 'topClientesList']);
    Route::get('/reportes/marcas/report', [ReportesController::class, 'topMarcasReport']);
    Route::get('/reportes/marcas/list', [ReportesController::class, 'topMarcasList']);
    Route::get('/reportes/recibos/report', [ReportesController::class, 'recibosReport']);
    Route::get('/reportes/recibos/list', [ReportesController::class, 'recibosList']);



    Route::get('/test/email',function(){
// $urlImagenes = env('URL_IMAGENES_PRODUCTOS');

// $SQL = "
//     SELECT
//         producto.id AS productoId,
//         producto.color,
//         producto.nombre AS nombreProducto,
//         marcaproducto.nombre AS nombreMarca,
//         COALESCE(
//             fotoproducto.url,
//             CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
//         ) AS imagen
//     FROM producto
//     LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
//     LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
//     WHERE producto.id IN (72325, 73879, 74479, 74045, 74050, 72324, 72355,
//     72309, 72437, 74468, 72306, 73347, 72278, 71812, 72239, 71539, 73023,
//     72965, 72960, 72905, 72528, 72517, 72295, 72246, 72296, 72036, 71946,
//     74440, 74439, 74257, 74166, 61461, 61669, 61670, 61686, 61919, 62252,
//     62306, 62945, 63429, 63551, 63859, 64125, 64412, 64523, 64559, 65084,
//     65097, 65147, 65431, 65434, 65679, 65684, 65693, 65714, 65715, 65718,
//     65856, 65901, 65955, 74627, 74630, 74631, 74641, 74652, 74646, 74650,
//     74653, 74655, 74656, 74657, 74664, 74666, 74669, 74817, 74672, 74678,
//     74680, 74682, 74692, 74819, 74724, 74725, 74728, 74740, 74748, 74751,
//     74753, 74755, 74770, 74785, 74791, 74793, 74794, 74801, 74814, 74809,
//     74820, 74804, 74823, 74825, 74828, 74829, 74834, 74836, 74845, 74847,
//     74848, 74852, 74856, 74858, 74864, 74865, 74866, 74870, 74872, 74875,
//     74878, 74881)
//     ORDER BY producto.precio ASC";

// $productos = DB::select($SQL);

// $html = '<table style="width:100%; border-collapse:collapse;">'; // Inicia la tabla principal
// $totalProductos = count($productos);

// // Estilos CSS inline
// $styleContainer = 'width: 100%; max-width: 600px; margin: 0 auto; text-align: center;';
// $styleRow = 'width: 100%; display: table-row;';
// $styleColumn = 'width: 33.33%; display: table-cell; padding: 10px; text-align: center;';
// $styleImg = 'max-width: 100%; height: auto; display: block; margin: 0 auto;';
// $styleTitle = 'font-size: 16px; font-weight: bold; color: #333; text-decoration: none; margin-top: 8px;';
// $styleDescription = 'font-size: 14px; color: #666; margin: 5px 0; text-align: center;';

// foreach ($productos as $index => $producto) {
//     if ($index % 3 === 0) {
//         $html .= '<tr style="' . $styleRow . '">';
//     }

//     $html .= '<td style="' . $styleColumn . '">
//                 <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '">
//                     <img src="' . $producto->imagen . '" alt="' . $producto->nombreProducto . '" style="' . $styleImg . '" width="120">
//                 </a>
//                 <br/>
//                 <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '" style="' . $styleTitle . '">
//                     ' . $producto->nombreMarca . '
//                 </a>
//                 <br/>
//                 <p style="' . $styleDescription . '">' . $producto->nombreProducto . ' | ' . $producto->color . '</p>
//               </td>';

//     if (($index + 1) % 3 === 0 || $index + 1 === $totalProductos) {
//         $html .= '</tr>';
//     }
// }

// $html .= '</table>'; // Cierra la tabla principal

// $payload = ["product" => $html];

// $response = Http::post(
//     'https://services.leadconnectorhq.com/hooks/40UecLU7dZ4KdLepJ7UR/webhook-trigger/3ee8c15e-7d8e-4149-b1a0-76414a16dd08',
//     $payload
// );

// if ($response->successful()) {
//     return response()->json(['status' => 'success', 'message' => 'Productos enviados correctamente.']);
// } else {
//     return response()->json(['status' => 'error', 'message' => 'Error al enviar los productos.', 'details' => $response->body()]);
// }
// });


$urlImagenes = env('URL_IMAGENES_PRODUCTOS');

$SQL = "
    SELECT
        producto.id AS productoId,
        producto.color,
        producto.nombre AS nombreProducto,
        marcaproducto.nombre AS nombreMarca,
        COALESCE(
            fotoproducto.url,
            CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
        ) AS imagen
    FROM producto
    LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
    LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
    WHERE producto.id IN (72325, 73879, 74479, 74045, 74050, 72324, 72355,
    72309, 72437, 74468, 72306, 73347, 72278, 71812, 72239, 71539, 73023,
    72965, 72960, 72905, 72528, 72517, 72295, 72246, 72296, 72036, 71946,
    74440, 74439, 74257, 74166, 61461, 61669, 61670, 61686, 61919, 62252,
    62306, 62945, 63429, 63551, 63859, 64125, 64412, 64523, 64559, 65084,
    65097, 65147, 65431, 65434, 65679, 65684, 65693, 65714, 65715, 65718,
    65856, 65901, 65955, 74627, 74630, 74631, 74641, 74652, 74646, 74650,
    74653, 74655, 74656, 74657, 74664, 74666, 74669, 74817, 74672, 74678,
    74680, 74682, 74692, 74819, 74724, 74725, 74728, 74740, 74748, 74751,
    74753, 74755, 74770, 74785, 74791, 74793, 74794, 74801, 74814, 74809,
    74820, 74804, 74823, 74825, 74828, 74829, 74834, 74836, 74845, 74847,
    74848, 74852, 74856, 74858, 74864, 74865, 74866, 74870, 74872, 74875,
    74878, 74881)
    ORDER BY producto.precio ASC";

$productos = DB::select($SQL);

$html = '<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Arrivals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 10px;
        }

        .product-item {
            width: calc(33.333% - 10px); /* 3 columnas con espacio entre ellas */
            box-sizing: border-box;
            text-align: center;
            vertical-align: top;
        }

        .product-item img {
            width: 120px;
            height: auto;
            border: 0;
            display: block;
            margin: 0 auto;
        }

        .product-title {
            font-size: 14px;
            font-weight: bold;
            color: #607C8B;
            text-decoration: none;
        }

        .product-description {
            font-size: 12px;
            color: #6C757B;
        }

        .footer {
            background-color: #354449;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }

        .footer div {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <table class="container" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 650px;">
        <!-- Header -->
        <tr>
            <td>
                <img src="https://mayoristasdeopticas.com/tienda/assets/imgs/logos/logo-ngo.png" alt="Logo"
                    style="width: 100%; height: auto;">
            </td>
        </tr>
        <tr>
            <td>
                <a href="https://mayoristasdeopticas.com/tienda/" target="_blank">
                    <img src="https://phpstack-1091339-3819555.cloudwaysapps.com/storage/newArrivalsBanner.png"
                        alt="New Arrivals">
                </a>
            </td>
        </tr>

        <!-- Productos -->
        <tr>
            <td>';

$html .= '<table style="width:100%; border-collapse:collapse;">'; // Inicia la tabla principal
$totalProductos = count($productos);

// Estilos CSS inline
$styleRow = 'width: 100%; display: table-row;';
$styleColumn = 'width: 33.33%; display: table-cell; padding: 10px; text-align: center;';
$styleImg = 'max-width: 100%; height: auto; display: block; margin: 0 auto;';
$styleTitle = 'font-size: 16px; font-weight: bold; color: #333; text-decoration: none; margin-top: 8px;';
$styleDescription = 'font-size: 14px; color: #666; margin: 5px 0; text-align: center;';

foreach ($productos as $index => $producto) {
    if ($index % 3 === 0) {
        $html .= '<tr style="' . $styleRow . '">';
    }

    $html .= '<td style="' . $styleColumn . '">
                <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '">
                    <img src="' . $producto->imagen . '" alt="' . $producto->nombreProducto . '" style="' . $styleImg . '" width="120">
                </a>
                <br/>
                <a href="https://mayoristasdeopticas.com/tienda/producto.php?id=' . $producto->productoId . '" style="' . $styleTitle . '">
                    ' . $producto->nombreMarca . '
                </a>
                <br/>
                <p style="' . $styleDescription . '">' . $producto->nombreProducto . ' | ' . $producto->color . '</p>
              </td>';

    if (($index + 1) % 3 === 0 || $index + 1 === $totalProductos) {
        $html .= '</tr>';
    }
}

$html .= '</table>'; // Cierra la tabla principal

$html .= '</td>
        </tr>

        <!-- Footer -->
        <tr>
            <td class="footer">
                <div>2618 NW 112th Ave. Miami, FL, 33172, EE.UU.</div>
                <div>+1 (305) 513-9177 / +1 (305) 513-9191</div>
                <div>Whatsapp servicio al cliente: +7868000990</div>
                <div>Ventas: +1 (305) 316-8267</div>
            </td>
        </tr>
                <tr>
            <td style="text-align:center">
               <a href="{{email.unsubscribe_link}}">Unsubscribe</a>
            </td>
        </tr>
    </table>

</body>

</html>';

// Imprimir el HTML generado en el navegador

// Mostrar el HTML como texto plano escapado
echo '<pre>' . htmlspecialchars($html) . '</pre>';


    //     Route::get('/test/email',function(){

    //         $urlImagenes = env('URL_IMAGENES_PRODUCTOS');

    //         $SQL = "
    //             SELECT
    //                 producto.id AS productoId,
    //                 producto.color,
    //                 producto.nombre AS nombreProducto,
    //                 marcaproducto.nombre AS nombreMarca,
    //                 COALESCE(
    //                     fotoproducto.url,
    //                     CONCAT('$urlImagenes', producto.imagenPrincipal, '.jpg')
    //                 ) AS imagen
    //             FROM producto
    //             LEFT JOIN marcaproducto ON producto.marca = marcaproducto.id
    //             LEFT JOIN fotoproducto ON fotoproducto.id = producto.imagenPrincipal
    //             ORDER BY producto.id DESC
    //             LIMIT 10";

    // // Realiza la consulta a la base de datos
    // //$productos = DB::select('SELECT producto.color, producto.nombre AS nombreProducto, marcaproducto.nombre AS nombreMarca FROM producto LEFT JOIN marcaproducto ON producto.marca=marcaproducto.id LIMIT 10');

    // $productos = DB::select($SQL);

    // return ['product'=>$productos];
    // // // Convierte el array de objetos en una colección
    // // $productos = collect($productos);

    // // // Ahora puedes usar chunk
    // // $productos = $productos->chunk(3);

    // //return json_encode($productos);

    // // // Enviar el correo
    // // Mail::to(['mgarralda@cobax.com.ar','alexiscobax1@gmail.com'])->send(new TestEmail($productos));

    // // return 'Correo de prueba enviado';


    //     // Mail::to(['mgarralda@cobax.com.ar','alexiscobax1@gmail.com'])->send(new TestEmail());
    //     // return 'Correo de prueba enviado';

    });

    Route::post('/test/ghl', [GoHighLevelController::class, 'getRefreshToken']);

    Route::post('/test/ghl/crear', [GoHighLevelController::class, 'createContact']);

    Route::post('/test/ghl/refresh', [GoHighLevelController::class, 'getRefreshToken']);

    Route::get('/test/nywd', [NywdController::class, 'getRefreshToken']);

    Route::post('/test/nywd/login', [NywdController::class, 'login']);

    Route::post('/test/nywd/refresh', [NywdController::class, 'refreshToken']);

    Route::post('/test/nywd/products', [NywdController::class, 'getProducts']);

    Route::post('/test/nywd/brands', [NywdController::class, 'getProductBrands']);

    Route::post('/test/nywd/categories', [NywdController::class, 'getProductCategories']);

    Route::post('/test/nywd/product/sku', [NywdController::class, 'getProductBySku']);


