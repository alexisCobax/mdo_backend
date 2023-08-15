<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\PortadaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ZipcodeController;
use App\Http\Controllers\Cliente2Controller;
use App\Http\Controllers\ComisionController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\OrderjetController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProspectoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReintegroController;
use App\Http\Controllers\CarritoWebController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\DescuentosController;
use App\Http\Controllers\InvoiceWebController;
use App\Http\Controllers\PlataformaController;
use App\Http\Controllers\TipobannerController;
use App\Http\Controllers\EtapapedidoController;
use App\Http\Controllers\FormadepagoController;
use App\Http\Controllers\GlobalToolsController;
use App\Http\Controllers\PedidocuponController;
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
use App\Http\Controllers\PagostarjetumController;
use App\Http\Controllers\PedidodetalleController;
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
use App\Http\Controllers\CategoriaproductoController;
use App\Http\Controllers\CotizaciondetalleController;
use App\Http\Controllers\CategoriafalabellaController;
use App\Http\Controllers\MovimientoproductoController;
use App\Http\Controllers\OrderjetdevolucionController;
use App\Http\Controllers\PlataformaproductoController;
use App\Http\Controllers\EmpresatransportadoraController;
use App\Http\Controllers\OrderjetdevoluciondetalleController;
use App\Http\Controllers\PedidodescuentospromocionController;
use App\Http\Controllers\PromocioncomprandoxgratiszController;

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


Route::middleware('auth:sanctum')->group(function () {

/** Banner Routes **/

Route::get('/banner', [BannerController::class, 'index']);
Route::get('/banner/{id}', [BannerController::class, 'show']);
Route::post('/banner', [BannerController::class, 'create']);
Route::put('/banner/{id}', [BannerController::class, 'update']);
Route::delete('/banner/{id}', [BannerController::class, 'delete']);


/** Carrier Routes **/

Route::get('/carrier', [CarrierController::class, 'index']);
Route::get('/carrier/{id}', [CarrierController::class, 'show']);
Route::post('/carrier', [CarrierController::class, 'create']);
Route::put('/carrier/{id}', [CarrierController::class, 'update']);
Route::delete('/carrier/{id}', [CarrierController::class, 'delete']);


/** Carriermethod Routes **/

Route::get('/carriermethod', [CarriermethodController::class, 'index']);
Route::get('/carriermethod/{id}', [CarriermethodController::class, 'show']);
Route::post('/carriermethod', [CarriermethodController::class, 'create']);
Route::put('/carriermethod/{id}', [CarriermethodController::class, 'update']);
Route::delete('/carriermethod/{id}', [CarriermethodController::class, 'delete']);


/** Carrito Routes **/

Route::get('/carrito', [CarritoController::class, 'index']);
Route::get('/carrito/{id}', [CarritoController::class, 'show']);
Route::post('/carrito', [CarritoController::class, 'create']);
Route::put('/carrito/{id}', [CarritoController::class, 'update']);
Route::delete('/carrito/{id}', [CarritoController::class, 'delete']);
Route::get('/carrito/status/{id}', [CarritoController::class, 'status']);


/** Carritodetalle Routes **/

Route::get('/carritodetalle', [CarritodetalleController::class, 'index']);
Route::get('/carritodetalle/{id}', [CarritodetalleController::class, 'show']);
Route::post('/carritodetalle', [CarritodetalleController::class, 'create']);
Route::put('/carritodetalle/{id}', [CarritodetalleController::class, 'update']);
Route::delete('/carritodetalle/{id}', [CarritodetalleController::class, 'delete']);


/** Categoriafalabella Routes **/

Route::get('/categoriafalabella', [CategoriafalabellaController::class, 'index']);
Route::get('/categoriafalabella/{id}', [CategoriafalabellaController::class, 'show']);
Route::post('/categoriafalabella', [CategoriafalabellaController::class, 'create']);
Route::put('/categoriafalabella/{id}', [CategoriafalabellaController::class, 'update']);
Route::delete('/categoriafalabella/{id}', [CategoriafalabellaController::class, 'delete']);


/** Categoriaproducto Routes **/

// Route::get('/categoriaproducto', [CategoriaproductoController::class, 'index']);
Route::get('/categoriaproducto/{id}', [CategoriaproductoController::class, 'show']);
Route::post('/categoriaproducto', [CategoriaproductoController::class, 'create']);
Route::put('/categoriaproducto/{id}', [CategoriaproductoController::class, 'update']);
Route::delete('/categoriaproducto/{id}', [CategoriaproductoController::class, 'delete']);


/** Ciudad Routes **/

Route::get('/ciudad', [CiudadController::class, 'index']);
Route::get('/ciudad/{id}', [CiudadController::class, 'show']);
Route::post('/ciudad', [CiudadController::class, 'create']);
Route::put('/ciudad/{id}', [CiudadController::class, 'update']);
Route::delete('/ciudad/{id}', [CiudadController::class, 'delete']);


/** Cliente Routes **/

Route::get('/cliente', [ClienteController::class, 'index']);
Route::get('/cliente/{id}', [ClienteController::class, 'show']);
Route::post('/cliente', [ClienteController::class, 'create']);
Route::put('/cliente/{id}', [ClienteController::class, 'update']);
Route::delete('/cliente/{id}', [ClienteController::class, 'delete']);


/** Cliente2 Routes **/

Route::get('/cliente2', [Cliente2Controller::class, 'index']);
Route::get('/cliente2/{id}', [Cliente2Controller::class, 'show']);
Route::post('/cliente2', [Cliente2Controller::class, 'create']);
Route::put('/cliente2/{id}', [Cliente2Controller::class, 'update']);
Route::delete('/cliente2/{id}', [Cliente2Controller::class, 'delete']);


/** Clientecontacto Routes **/

Route::get('/clientecontacto', [ClientecontactoController::class, 'index']);
Route::get('/clientecontacto/{id}', [ClientecontactoController::class, 'show']);
Route::post('/clientecontacto', [ClientecontactoController::class, 'create']);
Route::put('/clientecontacto/{id}', [ClientecontactoController::class, 'update']);
Route::delete('/clientecontacto/{id}', [ClientecontactoController::class, 'delete']);


/** Color Routes **/

Route::get('/color', [ColorController::class, 'index']);
Route::get('/color/{id}', [ColorController::class, 'show']);
Route::post('/color', [ColorController::class, 'create']);
Route::put('/color/{id}', [ColorController::class, 'update']);
Route::delete('/color/{id}', [ColorController::class, 'delete']);


/** Comision Routes **/

Route::get('/comision', [ComisionController::class, 'index']);
Route::get('/comision/{id}', [ComisionController::class, 'show']);
Route::post('/comision', [ComisionController::class, 'create']);
Route::put('/comision/{id}', [ComisionController::class, 'update']);
Route::delete('/comision/{id}', [ComisionController::class, 'delete']);


/** Compra Routes **/

Route::get('/compra', [CompraController::class, 'index']);
Route::get('/compra/{id}', [CompraController::class, 'show']);
Route::post('/compra', [CompraController::class, 'create']);
Route::put('/compra/{id}', [CompraController::class, 'update']);
Route::delete('/compra/{id}', [CompraController::class, 'delete']);


/** Compradetalle Routes **/

Route::get('/compradetalle', [CompradetalleController::class, 'index']);
Route::get('/compradetalle/{id}', [CompradetalleController::class, 'show']);
Route::post('/compradetalle', [CompradetalleController::class, 'create']);
Route::put('/compradetalle/{id}', [CompradetalleController::class, 'update']);
Route::delete('/compradetalle/{id}', [CompradetalleController::class, 'delete']);


/** Compradetallenn Routes **/

Route::get('/compradetallenn', [CompradetallennController::class, 'index']);
Route::get('/compradetallenn/{id}', [CompradetallennController::class, 'show']);
Route::post('/compradetallenn', [CompradetallennController::class, 'create']);
Route::put('/compradetallenn/{id}', [CompradetallennController::class, 'update']);
Route::delete('/compradetallenn/{id}', [CompradetallennController::class, 'delete']);


/** Configuracion Routes **/

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

/** CotizacionPedido - PedidoCotizacionRoutes **/

Route::post('/cotizacion/pedido', [CotizacionPedidoController::class, 'create']);
Route::post('/pedido/cotizacion', [PedidoCotizacionController::class, 'create']);

/** Cupondescuento Routes **/

Route::get('/cupondescuento', [CupondescuentoController::class, 'index']);
Route::get('/cupondescuento/{id}', [CupondescuentoController::class, 'show']);
Route::post('/cupondescuento', [CupondescuentoController::class, 'create']);
Route::put('/cupondescuento/{id}', [CupondescuentoController::class, 'update']);
Route::delete('/cupondescuento/{id}', [CupondescuentoController::class, 'delete']);

/** CuentaCorriente Routes **/

Route::get('/cuentacorriente/{id}', [CuentaCorrienteController::class, 'show']);


/** Deposito Routes **/

Route::get('/deposito', [DepositoController::class, 'index']);
Route::get('/deposito/{id}', [DepositoController::class, 'show']);
Route::post('/deposito', [DepositoController::class, 'create']);
Route::put('/deposito/{id}', [DepositoController::class, 'update']);
Route::delete('/deposito/{id}', [DepositoController::class, 'delete']);

/** Descuentos **/

Route::post('/descuentos', [DescuentosController::class, 'index']);


/** Empleado Routes **/

Route::get('/empleado', [EmpleadoController::class, 'index']);
Route::get('/empleado/{id}', [EmpleadoController::class, 'show']);
Route::post('/empleado', [EmpleadoController::class, 'create']);
Route::put('/empleado/{id}', [EmpleadoController::class, 'update']);
Route::delete('/empleado/{id}', [EmpleadoController::class, 'delete']);


/** Empresatransportadora Routes **/

Route::get('/empresatransportadora', [EmpresatransportadoraController::class, 'index']);
Route::get('/empresatransportadora/{id}', [EmpresatransportadoraController::class, 'show']);
Route::post('/empresatransportadora', [EmpresatransportadoraController::class, 'create']);
Route::put('/empresatransportadora/{id}', [EmpresatransportadoraController::class, 'update']);
Route::delete('/empresatransportadora/{id}', [EmpresatransportadoraController::class, 'delete']);


/** Encargadodeventa Routes **/

Route::get('/encargadodeventa', [EncargadodeventaController::class, 'index']);
Route::get('/encargadodeventa/{id}', [EncargadodeventaController::class, 'show']);
Route::post('/encargadodeventa', [EncargadodeventaController::class, 'create']);
Route::put('/encargadodeventa/{id}', [EncargadodeventaController::class, 'update']);
Route::delete('/encargadodeventa/{id}', [EncargadodeventaController::class, 'delete']);


/** Estadocotizacion Routes **/

Route::get('/estadocotizacion', [EstadocotizacionController::class, 'index']);
Route::get('/estadocotizacion/{id}', [EstadocotizacionController::class, 'show']);
Route::post('/estadocotizacion', [EstadocotizacionController::class, 'create']);
Route::put('/estadocotizacion/{id}', [EstadocotizacionController::class, 'update']);
Route::delete('/estadocotizacion/{id}', [EstadocotizacionController::class, 'delete']);


/** Estadopedido Routes **/

Route::get('/estadopedido', [EstadopedidoController::class, 'index']);
Route::get('/estadopedido/{id}', [EstadopedidoController::class, 'show']);
Route::post('/estadopedido', [EstadopedidoController::class, 'create']);
Route::put('/estadopedido/{id}', [EstadopedidoController::class, 'update']);
Route::delete('/estadopedido/{id}', [EstadopedidoController::class, 'delete']);


/** Estuche Routes **/

Route::get('/estuche', [EstucheController::class, 'index']);
Route::get('/estuche/{id}', [EstucheController::class, 'show']);
Route::post('/estuche', [EstucheController::class, 'create']);
Route::put('/estuche/{id}', [EstucheController::class, 'update']);
Route::delete('/estuche/{id}', [EstucheController::class, 'delete']);


/** Etapapedido Routes **/

Route::get('/etapapedido', [EtapapedidoController::class, 'index']);
Route::get('/etapapedido/{id}', [EtapapedidoController::class, 'show']);
Route::post('/etapapedido', [EtapapedidoController::class, 'create']);
Route::put('/etapapedido/{id}', [EtapapedidoController::class, 'update']);
Route::delete('/etapapedido/{id}', [EtapapedidoController::class, 'delete']);


/** Formadepago Routes **/

Route::get('/formadepago', [FormadepagoController::class, 'index']);
Route::get('/formadepago/{id}', [FormadepagoController::class, 'show']);
Route::post('/formadepago', [FormadepagoController::class, 'create']);
Route::put('/formadepago/{id}', [FormadepagoController::class, 'update']);
Route::delete('/formadepago/{id}', [FormadepagoController::class, 'delete']);


/** Fotoproducto Routes **/

Route::get('/fotoproducto', [FotoproductoController::class, 'index']);
Route::get('/fotoproducto/{id}', [FotoproductoController::class, 'show']);
Route::post('/fotoproducto', [FotoproductoController::class, 'create']);
Route::put('/fotoproducto/{id}', [FotoproductoController::class, 'update']);
Route::delete('/fotoproducto/{id}', [FotoproductoController::class, 'delete']);


/** Grupo Routes **/

Route::post('/grupo', [GrupoController::class, 'create']);
Route::put('/grupo/{id}', [GrupoController::class, 'update']);
Route::delete('/grupo/{id}', [GrupoController::class, 'delete']);


/** Globals Routes **/

Route::get('/global', [GlobalToolsController::class, 'index']);


/** Invoice Routes **/

Route::get('/invoice', [InvoiceController::class, 'index']);
Route::get('/invoice/{id}', [InvoiceController::class, 'show']);
Route::post('/invoice', [InvoiceController::class, 'create']);
Route::put('/invoice/{id}', [InvoiceController::class, 'update']);
Route::delete('/invoice/{id}', [InvoiceController::class, 'delete']);


/** Invoicedetalle Routes **/

Route::get('/invoicedetalle', [InvoicedetalleController::class, 'index']);
Route::get('/invoicedetalle/{id}', [InvoicedetalleController::class, 'show']);
Route::post('/invoicedetalle', [InvoicedetalleController::class, 'create']);
Route::put('/invoicedetalle/{id}', [InvoicedetalleController::class, 'update']);
Route::delete('/invoicedetalle/{id}', [InvoicedetalleController::class, 'delete']);


/** Jet Routes **/

Route::get('/jet', [JetController::class, 'index']);
Route::get('/jet/{id}', [JetController::class, 'show']);
Route::post('/jet', [JetController::class, 'create']);
Route::put('/jet/{id}', [JetController::class, 'update']);
Route::delete('/jet/{id}', [JetController::class, 'delete']);


/** Marcafalabella Routes **/

Route::get('/marcafalabella', [MarcafalabellaController::class, 'index']);
Route::get('/marcafalabella/{id}', [MarcafalabellaController::class, 'show']);
Route::post('/marcafalabella', [MarcafalabellaController::class, 'create']);
Route::put('/marcafalabella/{id}', [MarcafalabellaController::class, 'update']);
Route::delete('/marcafalabella/{id}', [MarcafalabellaController::class, 'delete']);


/** Marcaproducto Routes **/

Route::get('/marcaproducto', [MarcaproductoController::class, 'index']);
Route::get('/marcaproducto/{id}', [MarcaproductoController::class, 'show']);
Route::post('/marcaproducto', [MarcaproductoController::class, 'create']);
Route::put('/marcaproducto/{id}', [MarcaproductoController::class, 'update']);
Route::delete('/marcaproducto/{id}', [MarcaproductoController::class, 'delete']);


/** Materialproducto Routes **/

Route::get('/materialproducto', [MaterialproductoController::class, 'index']);
Route::get('/materialproducto/{id}', [MaterialproductoController::class, 'show']);
Route::post('/materialproducto', [MaterialproductoController::class, 'create']);
Route::put('/materialproducto/{id}', [MaterialproductoController::class, 'update']);
Route::delete('/materialproducto/{id}', [MaterialproductoController::class, 'delete']);


/** Moneda Routes **/

Route::get('/moneda', [MonedaController::class, 'index']);
Route::get('/moneda/{id}', [MonedaController::class, 'show']);
Route::post('/moneda', [MonedaController::class, 'create']);
Route::put('/moneda/{id}', [MonedaController::class, 'update']);
Route::delete('/moneda/{id}', [MonedaController::class, 'delete']);


/** Movimientoproducto Routes **/

Route::get('/movimientoproducto', [MovimientoproductoController::class, 'index']);
Route::get('/movimientoproducto/{id}', [MovimientoproductoController::class, 'show']);
Route::post('/movimientoproducto', [MovimientoproductoController::class, 'create']);
Route::put('/movimientoproducto/{id}', [MovimientoproductoController::class, 'update']);
Route::delete('/movimientoproducto/{id}', [MovimientoproductoController::class, 'delete']);


/** Orderjet Routes **/

Route::get('/orderjet', [OrderjetController::class, 'index']);
Route::get('/orderjet/{id}', [OrderjetController::class, 'show']);
Route::post('/orderjet', [OrderjetController::class, 'create']);
Route::put('/orderjet/{id}', [OrderjetController::class, 'update']);
Route::delete('/orderjet/{id}', [OrderjetController::class, 'delete']);


/** Orderjetdevolucion Routes **/

Route::get('/orderjetdevolucion', [OrderjetdevolucionController::class, 'index']);
Route::get('/orderjetdevolucion/{id}', [OrderjetdevolucionController::class, 'show']);
Route::post('/orderjetdevolucion', [OrderjetdevolucionController::class, 'create']);
Route::put('/orderjetdevolucion/{id}', [OrderjetdevolucionController::class, 'update']);
Route::delete('/orderjetdevolucion/{id}', [OrderjetdevolucionController::class, 'delete']);


/** Orderjetdevoluciondetalle Routes **/

Route::get('/orderjetdevoluciondetalle', [OrderjetdevoluciondetalleController::class, 'index']);
Route::get('/orderjetdevoluciondetalle/{id}', [OrderjetdevoluciondetalleController::class, 'show']);
Route::post('/orderjetdevoluciondetalle', [OrderjetdevoluciondetalleController::class, 'create']);
Route::put('/orderjetdevoluciondetalle/{id}', [OrderjetdevoluciondetalleController::class, 'update']);
Route::delete('/orderjetdevoluciondetalle/{id}', [OrderjetdevoluciondetalleController::class, 'delete']);


/** Origenpedido Routes **/

Route::get('/origenpedido', [OrigenpedidoController::class, 'index']);
Route::get('/origenpedido/{id}', [OrigenpedidoController::class, 'show']);
Route::post('/origenpedido', [OrigenpedidoController::class, 'create']);
Route::put('/origenpedido/{id}', [OrigenpedidoController::class, 'update']);
Route::delete('/origenpedido/{id}', [OrigenpedidoController::class, 'delete']);


/** Pagostarjetum Routes **/

Route::get('/pagostarjetum', [PagostarjetumController::class, 'index']);
Route::get('/pagostarjetum/{id}', [PagostarjetumController::class, 'show']);
Route::post('/pagostarjetum', [PagostarjetumController::class, 'create']);
Route::put('/pagostarjetum/{id}', [PagostarjetumController::class, 'update']);
Route::delete('/pagostarjetum/{id}', [PagostarjetumController::class, 'delete']);


/** Pais Routes **/

Route::get('/pais', [PaisController::class, 'index']);
Route::get('/pais/{id}', [PaisController::class, 'show']);
Route::post('/pais', [PaisController::class, 'create']);
Route::put('/pais/{id}', [PaisController::class, 'update']);
Route::delete('/pais/{id}', [PaisController::class, 'delete']);


/** Paypal Routes **/

Route::get('/paypal', [PaypalController::class, 'index']);
Route::get('/paypal/{id}', [PaypalController::class, 'show']);
Route::post('/paypal', [PaypalController::class, 'create']);
Route::put('/paypal/{id}', [PaypalController::class, 'update']);
Route::delete('/paypal/{id}', [PaypalController::class, 'delete']);


/** Pedido Routes **/

Route::get('/pedido', [PedidoController::class, 'index']);
Route::get('/pedido/{id}', [PedidoController::class, 'show']);
Route::post('/pedido', [PedidoController::class, 'create']);
Route::put('/pedido/{id}', [PedidoController::class, 'update']);
Route::delete('/pedido/{id}', [PedidoController::class, 'delete']);


/** Pedidocupon Routes **/

Route::get('/pedidocupon', [PedidocuponController::class, 'index']);
Route::get('/pedidocupon/{id}', [PedidocuponController::class, 'show']);
Route::post('/pedidocupon', [PedidocuponController::class, 'create']);
Route::put('/pedidocupon/{id}', [PedidocuponController::class, 'update']);
Route::delete('/pedidocupon/{id}', [PedidocuponController::class, 'delete']);


/** Pedidodescuentospromocion Routes **/

Route::get('/pedidodescuentospromocion', [PedidodescuentospromocionController::class, 'index']);
Route::get('/pedidodescuentospromocion/{id}', [PedidodescuentospromocionController::class, 'show']);
Route::post('/pedidodescuentospromocion', [PedidodescuentospromocionController::class, 'create']);
Route::put('/pedidodescuentospromocion/{id}', [PedidodescuentospromocionController::class, 'update']);
Route::delete('/pedidodescuentospromocion/{id}', [PedidodescuentospromocionController::class, 'delete']);


/** Pedidodetalle Routes **/

Route::get('/pedidodetalle', [PedidodetalleController::class, 'index']);
Route::get('/pedidodetalle/{id}', [PedidodetalleController::class, 'show']);
Route::post('/pedidodetalle', [PedidodetalleController::class, 'create']);
Route::put('/pedidodetalle/{id}', [PedidodetalleController::class, 'update']);
Route::delete('/pedidodetalle/{id}', [PedidodetalleController::class, 'delete']);


/** Pedidodetallenn Routes **/

Route::get('/pedidodetallenn', [PedidodetallennController::class, 'index']);
Route::get('/pedidodetallenn/{id}', [PedidodetallennController::class, 'show']);
Route::post('/pedidodetallenn', [PedidodetallennController::class, 'create']);
Route::put('/pedidodetallenn/{id}', [PedidodetallennController::class, 'update']);
Route::delete('/pedidodetallenn/{id}', [PedidodetallennController::class, 'delete']);


/** Perfil Routes **/

Route::get('/perfil', [PerfilController::class, 'index']);
Route::get('/perfil/{id}', [PerfilController::class, 'show']);
Route::post('/perfil', [PerfilController::class, 'create']);
Route::put('/perfil/{id}', [PerfilController::class, 'update']);
Route::delete('/perfil/{id}', [PerfilController::class, 'delete']);


/** Plataforma Routes **/

Route::get('/plataforma', [PlataformaController::class, 'index']);
Route::get('/plataforma/{id}', [PlataformaController::class, 'show']);
Route::post('/plataforma', [PlataformaController::class, 'create']);
Route::put('/plataforma/{id}', [PlataformaController::class, 'update']);
Route::delete('/plataforma/{id}', [PlataformaController::class, 'delete']);


/** Plataformaproducto Routes **/

Route::get('/plataformaproducto', [PlataformaproductoController::class, 'index']);
Route::get('/plataformaproducto/{id}', [PlataformaproductoController::class, 'show']);
Route::post('/plataformaproducto', [PlataformaproductoController::class, 'create']);
Route::put('/plataformaproducto/{id}', [PlataformaproductoController::class, 'update']);
Route::delete('/plataformaproducto/{id}', [PlataformaproductoController::class, 'delete']);


/** Portada Routes **/

Route::get('/portada', [PortadaController::class, 'index']);
Route::get('/portada/{id}', [PortadaController::class, 'show']);
Route::post('/portada', [PortadaController::class, 'create']);
Route::put('/portada/{id}', [PortadaController::class, 'update']);
Route::delete('/portada/{id}', [PortadaController::class, 'delete']);


/** Producto Routes **/

// Route::get('/producto', [ProductoController::class, 'index']);
// Route::get('/producto/{id}', [ProductoController::class, 'show']);
Route::get('/producto/stock/{id}', [ProductoController::class, 'stock']);
Route::post('/producto', [ProductoController::class, 'create']);
Route::put('/producto/{id}', [ProductoController::class, 'update']);
Route::delete('/producto/{id}', [ProductoController::class, 'delete']);


/** Productogenero Routes **/

Route::get('/productogenero', [ProductogeneroController::class, 'index']);
Route::get('/productogenero/{id}', [ProductogeneroController::class, 'show']);
Route::post('/productogenero', [ProductogeneroController::class, 'create']);
Route::put('/productogenero/{id}', [ProductogeneroController::class, 'update']);
Route::delete('/productogenero/{id}', [ProductogeneroController::class, 'delete']);


/** Promocioncomprandoxgratisz Routes **/

Route::get('/promocioncomprandoxgratisz', [PromocioncomprandoxgratiszController::class, 'index']);
Route::get('/promocioncomprandoxgratisz/{id}', [PromocioncomprandoxgratiszController::class, 'show']);
Route::post('/promocioncomprandoxgratisz', [PromocioncomprandoxgratiszController::class, 'create']);
Route::put('/promocioncomprandoxgratisz/{id}', [PromocioncomprandoxgratiszController::class, 'update']);
Route::delete('/promocioncomprandoxgratisz/{id}', [PromocioncomprandoxgratiszController::class, 'delete']);


/** Prospecto Routes **/

Route::get('/prospecto', [ProspectoController::class, 'index']);
Route::get('/prospecto/{id}', [ProspectoController::class, 'show']);
Route::post('/prospecto', [ProspectoController::class, 'create']);
Route::put('/prospecto/{id}', [ProspectoController::class, 'update']);
Route::delete('/prospecto/{id}', [ProspectoController::class, 'delete']);


/** Proveedor Routes **/

Route::get('/proveedor', [ProveedorController::class, 'index']);
Route::get('/proveedor/{id}', [ProveedorController::class, 'show']);
Route::post('/proveedor', [ProveedorController::class, 'create']);
Route::put('/proveedor/{id}', [ProveedorController::class, 'update']);
Route::delete('/proveedor/{id}', [ProveedorController::class, 'delete']);


/** Puesto Routes **/

Route::get('/puesto', [PuestoController::class, 'index']);
Route::get('/puesto/{id}', [PuestoController::class, 'show']);
Route::post('/puesto', [PuestoController::class, 'create']);
Route::put('/puesto/{id}', [PuestoController::class, 'update']);
Route::delete('/puesto/{id}', [PuestoController::class, 'delete']);

/** Excel To Json Route**/
Route::post('/procesar-excel', [ExcelController::class, 'procesarExcel']);

/** Recibo Routes **/

Route::get('/recibo', [ReciboController::class, 'index']);
Route::get('/recibo/{id}', [ReciboController::class, 'show']);
Route::post('/recibo', [ReciboController::class, 'create']);
Route::put('/recibo/{id}', [ReciboController::class, 'update']);
Route::delete('/recibo/{id}', [ReciboController::class, 'delete']);


/** Reintegro Routes **/

Route::get('/reintegro', [ReintegroController::class, 'index']);
Route::get('/reintegro/{id}', [ReintegroController::class, 'show']);
Route::post('/reintegro', [ReintegroController::class, 'create']);
Route::put('/reintegro/{id}', [ReintegroController::class, 'update']);
Route::delete('/reintegro/{id}', [ReintegroController::class, 'delete']);


/** Sesion Routes **/

Route::get('/sesion', [SesionController::class, 'index']);
Route::get('/sesion/{id}', [SesionController::class, 'show']);
Route::post('/sesion', [SesionController::class, 'create']);
Route::put('/sesion/{id}', [SesionController::class, 'update']);
Route::delete('/sesion/{id}', [SesionController::class, 'delete']);


/** Sexoproducto Routes **/

Route::get('/sexoproducto', [SexoproductoController::class, 'index']);
Route::get('/sexoproducto/{id}', [SexoproductoController::class, 'show']);
Route::post('/sexoproducto', [SexoproductoController::class, 'create']);
Route::put('/sexoproducto/{id}', [SexoproductoController::class, 'update']);
Route::delete('/sexoproducto/{id}', [SexoproductoController::class, 'delete']);


/** Subidasfalabella Routes **/

Route::get('/subidasfalabella', [SubidasfalabellaController::class, 'index']);
Route::get('/subidasfalabella/{id}', [SubidasfalabellaController::class, 'show']);
Route::post('/subidasfalabella', [SubidasfalabellaController::class, 'create']);
Route::put('/subidasfalabella/{id}', [SubidasfalabellaController::class, 'update']);
Route::delete('/subidasfalabella/{id}', [SubidasfalabellaController::class, 'delete']);


/** Tamanoproducto Routes **/

Route::get('/tamanoproducto', [TamanoproductoController::class, 'index']);
Route::get('/tamanoproducto/{id}', [TamanoproductoController::class, 'show']);
Route::post('/tamanoproducto', [TamanoproductoController::class, 'create']);
Route::put('/tamanoproducto/{id}', [TamanoproductoController::class, 'update']);
Route::delete('/tamanoproducto/{id}', [TamanoproductoController::class, 'delete']);


/** Tipobanner Routes **/

Route::get('/tipobanner', [TipobannerController::class, 'index']);
Route::get('/tipobanner/{id}', [TipobannerController::class, 'show']);
Route::post('/tipobanner', [TipobannerController::class, 'create']);
Route::put('/tipobanner/{id}', [TipobannerController::class, 'update']);
Route::delete('/tipobanner/{id}', [TipobannerController::class, 'delete']);


/** Tipodeenvio Routes **/

Route::get('/tipodeenvio', [TipodeenvioController::class, 'index']);
Route::get('/tipodeenvio/{id}', [TipodeenvioController::class, 'show']);
Route::post('/tipodeenvio', [TipodeenvioController::class, 'create']);
Route::put('/tipodeenvio/{id}', [TipodeenvioController::class, 'update']);
Route::delete('/tipodeenvio/{id}', [TipodeenvioController::class, 'delete']);


/** Tipoproducto Routes **/

Route::get('/tipoproducto', [TipoproductoController::class, 'index']);
Route::get('/tipoproducto/{id}', [TipoproductoController::class, 'show']);
Route::post('/tipoproducto', [TipoproductoController::class, 'create']);
Route::put('/tipoproducto/{id}', [TipoproductoController::class, 'update']);
Route::delete('/tipoproducto/{id}', [TipoproductoController::class, 'delete']);


/** Transaccion Routes **/

Route::get('/transaccion', [TransaccionController::class, 'index']);
Route::get('/transaccion/{id}', [TransaccionController::class, 'show']);
Route::post('/transaccion', [TransaccionController::class, 'create']);
Route::put('/transaccion/{id}', [TransaccionController::class, 'update']);
Route::delete('/transaccion/{id}', [TransaccionController::class, 'delete']);


/** Usuario Routes **/

Route::get('/usuario', [UsuarioController::class, 'index']);
Route::get('/usuario/{id}', [UsuarioController::class, 'show']); 
Route::post('/usuario', [UsuarioController::class, 'create']);
Route::put('/usuario/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuario/{id}', [UsuarioController::class, 'delete']);


/** Zipcode Routes **/

Route::get('/zipcode', [ZipcodeController::class, 'index']);
Route::get('/zipcode/{id}', [ZipcodeController::class, 'show']);
Route::post('/zipcode', [ZipcodeController::class, 'create']);
Route::put('/zipcode/{id}', [ZipcodeController::class, 'update']);
Route::delete('/zipcode/{id}', [ZipcodeController::class, 'delete']);

/** Tools Routes **/

Route::post('/excel',[ExcelController::class,'index']);

/** Upload Imagenes **/

Route::post('/upload/images', [ImageController::class, 'upload'])->name('upload.images');

/** Login Routes **/

Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']);
Route::post('me', [AuthController::class, 'me']);

/** WEB Routes **/

Route::post('web/carrito/status', [CarritoWebController::class, 'show']);
Route::post('web/invoice', [InvoiceWebController::class, 'index']);
Route::post('web/cotizaciones', [CotizacionesWebController::class, 'index']);
Route::post('web/usuario/password', [AuthWebController::class, 'change']);

});

/** Login Routes Not Auth **/

Route::post('login', [AuthController::class, 'login']);

/** Categorias Routes Not Auth **/

Route::get('/categoriaproducto', [CategoriaproductoController::class, 'index']);

/** Grupo Routes Not Auth **/

Route::get('/grupo', [GrupoController::class, 'index']);
Route::get('/grupo/{id}', [GrupoController::class, 'show']);

/** Producto Routes Not Auth **/

Route::get('/producto', [ProductoController::class, 'index']);
Route::get('/producto/{id}', [ProductoController::class, 'show']);

Route::get('/pdf/proforma', [PdfController::class, 'proforma']);
Route::get('/pdf/factura', [PdfController::class, 'factura']);
Route::get('/pdf/recibo', [PdfController::class, 'recibo']);


