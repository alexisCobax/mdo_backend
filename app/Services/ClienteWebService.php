<?php

namespace App\Services;

use App\Helpers\PaginateHelper;
use App\Mail\EnvioCotizacionMailSinAdjunto;
use App\Models\Cliente;
use App\Models\Pais;
use App\Models\Usuario;
use App\Transformers\Cliente\CreateWebTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClienteWebService
{
    public function findAll(Request $request)
    {
        try {
            $data = PaginateHelper::getPaginatedData($request, Cliente::class);

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los clientes'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function find(Request $request)
    {
        try {
            $query = $request->input('nombre');

            $data = Cliente::where('nombre', 'LIKE', "%$query%")
            ->select('id', 'nombre')
            ->get()
            ->pluck('nombre', 'id');

            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al obtener los clientes'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function findByToken(Request $request)
    {
        $user = Auth::user();

        $data = Cliente::with('usuarios')->where('usuario', $user->id)->first();

        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    public function create(Request $request)
    {

        $existeEmail = Cliente::where('email', $request->email)->count();
        if ($existeEmail != 0) {
            return response()->json(['error' => 'Email existente', 'status' => 203], Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }

        $existeUsuario = Usuario::where('nombre', $request->usuario)->count();
        if ($existeUsuario != 0) {
            return response()->json(['error' => 'Usuario existente', 'status' => 203], Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }

        $usuario = [
            'nombre' => $request->email,
            'clave' => $request->clave ? Hash::make($request->clave) : str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT),
            'permisos' => 2,
            'suspendido' => 0,
        ];

        $usuario = Usuario::create($usuario);

        if (!$usuario) {
            return response()->json(['error' => 'Failed to create Usuario'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $pais = Pais::where('id', $request->pais)->first();
        if (!$pais) {
            return response()->json(['error' => 'Pais no encontrado'], Response::HTTP_NOT_FOUND);
        }

        // ---- NORMALIZAR COUNTRY A ISO2 (para API de GoHighLevel) ----

$countryRaw = trim(strtolower($pais->codigo ?? ''));

// mapa para toda América
$countryMap = [

    // Sudamérica
    'argentina' => 'AR', 'arg' => 'AR', 'ar' => 'AR',
    'brasil' => 'BR', 'brazil' => 'BR', 'bra' => 'BR', 'br' => 'BR',
    'chile' => 'CL', 'chl' => 'CL', 'cl' => 'CL',
    'uruguay' => 'UY', 'ury' => 'UY', 'uy' => 'UY',
    'paraguay' => 'PY', 'pry' => 'PY', 'py' => 'PY',
    'bolivia' => 'BO', 'bol' => 'BO', 'bo' => 'BO',
    'peru' => 'PE', 'per' => 'PE', 'pe' => 'PE',
    'ecuador' => 'EC', 'ecu' => 'EC', 'ec' => 'EC',
    'colombia' => 'CO', 'col' => 'CO', 'co' => 'CO',
    'venezuela' => 'VE', 'ven' => 'VE', 've' => 'VE',
    'guyana' => 'GY', 'gy' => 'GY',
    'suriname' => 'SR', 'sr' => 'SR',

    // Centroamérica
    'mexico' => 'MX', 'mex' => 'MX', 'mx' => 'MX',
    'guatemala' => 'GT', 'gt' => 'GT',
    'honduras' => 'HN', 'hn' => 'HN',
    'elsalvador' => 'SV', 'el salvador' => 'SV', 'sv' => 'SV',
    'nicaragua' => 'NI', 'ni' => 'NI',
    'costarica' => 'CR', 'costa rica' => 'CR', 'cr' => 'CR',
    'panama' => 'PA', 'pa' => 'PA',

    // Norteamérica
    'estados unidos' => 'US', 'united states' => 'US', 'usa' => 'US', 'us' => 'US',
    'canada' => 'CA', 'ca' => 'CA',

    // Caribe principales
    'cuba' => 'CU', 'cu' => 'CU',
    'republica dominicana' => 'DO', 'dominican republic' => 'DO', 'do' => 'DO',
    'puerto rico' => 'PR', 'pr' => 'PR',
];

// resolver country
$countryISO2 = $countryMap[$countryRaw] ?? strtoupper(substr($pais->codigo, 0, 2));

// fallback seguro (API de :contentReference[oaicite:0]{index=0} exige valor válido)
if (!$countryISO2 || strlen($countryISO2) != 2) {
    $countryISO2 = 'AR';
}

        $payload = [
            "firstName" => $request->nombre,
            "lastName" => $request->nombre,
            "name" => $request->nombre,
            "email" => $request->email,
            "phone" => $request->telefono,
            "address1" => $request->direccion,
            "city" => $request->ciudad, 
            "country" => $countryISO2,
            "postalCode" => $request->codigoPostal,
            "tags" => ["masterlist"]
        ];
        

        //-------------------------------------------------------

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://rest.gohighlevel.com/v1/contacts/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode($payload),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJsb2NhdGlvbl9pZCI6IjQwVWVjTFU3ZFo0S2RMZXBKN1VSIiwidmVyc2lvbiI6MSwiaWF0IjoxNzIzMTUxNjE1Mzc3LCJzdWIiOiIyNUJiU0sybjhOMjR3dHFiU3MxVSJ9.DIcblz5hF35Hr1XO_w9mM0TboQCJhQ_YtWckwibBqbc'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;


        //-------------------------------------------------------

        // GoHighLevelService::createContact($payload);

        $transformer = new CreateWebTransformer();
        $cliente = $transformer->transform($request, $usuario->id);
        $nombre = (!empty($request->nombre)) ? $request->nombre : '';
        $usuario = (!empty($request->email)) ? $request->email : '';
        $clave = (!empty($request->clave)) ? $request->clave : '';

        $cliente = Cliente::create($cliente);

        if (!$cliente) {
            return response()->json(['error' => 'Failed to create Cliente'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        /* Envio email a cliente **/
        // try {
        //     $cuerpo = 'pdf.alta_cliente';
        //     $subject = 'Nueva Alta';
        //     $destinatarios = [
        //         $request->email
        //     ];

        //     Mail::to($destinatarios)->send(new EnvioCotizacionMailSinAdjunto($cuerpo, $subject, $nombre, $usuario, $clave));

        /* Envio email a cliente **/
        try {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://services.leadconnectorhq.com/hooks/40UecLU7dZ4KdLepJ7UR/webhook-trigger/55fead57-9600-4280-ae96-73f0b9b2e5c1',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS =>'{
            "email":"'.$usuario.'",
            "usuario":"'.$usuario.'",
            "nombre-completo": "'.$nombre.'",
            "clave" : "'.$clave.'"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

            return response()->json(['Response' => 'Enviado Correctamente'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($cliente, Response::HTTP_OK);
    }

    public function update(Request $request)
    {

        $user = Auth::user();

        $usuario = Usuario::findOrFail($user->id);
        $cliente = Cliente::where('usuario', $user->id);

        if ($request->usuario) {
            $dataUsuario = [
                'id' => $usuario->id,
                'nombre' => $request->usuario,
                'permisos' => 1,
                'suspendido' => 0,
            ];

            $usuario->update($dataUsuario);
        }

        if ($request->clave) {
            $dataUsuario = [
                'id' => $usuario->id,
                'clave' => $request->clave,
                'permisos' => 1,
                'suspendido' => 0,
            ];

            $usuario->update($dataUsuario);
        }

        if ($cliente) {
            $camposActualizables = [
                'nombre', 'direccion', 'codigoPostal', 'telefono', 'email',
                'fax', 'contacto', 'puestoContacto', 'transportadora', 'telefonoTransportadora',
                'observaciones', 'usuario', 'suspendido', 'web', 'direccionShape',
                'direccionBill', 'vendedor', 'ciudad', 'pais', 'usuarioVIP',
                'claveVIP', 'VIP', 'ctacte', 'cpShape', 'paisShape',
                'primeraCompra', 'cantidadDeCompras', 'idAgile', 'montoMaximoDePago', 'WhatsApp',
                'Notas', 'tipoDeEnvio', 'nombreEnvio', 'regionEnvio', 'ciudadEnvio',
                'fechaAlta', 'ipAlta', 'ultimoLogin', 'ipUltimoLogin', 'prospecto',
                'contactoApellido', 'IdActiveCampaign', 'IdActiveCampaignContact', 'notification',
            ];
            $dataCliente = $request->only($camposActualizables);

            $cliente->update($dataCliente);
        }

        $response = [
            $cliente->get(),
            'usuario' => [
                'nombre' => $usuario->nombre,
                'permisos' => $usuario->permisos,
                'suspendido' => $usuario->suspendido,
            ],
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        $cliente = Cliente::find($request->id);

        if (!$cliente) {
            return response()->json(['error' => 'Cliente not found'], Response::HTTP_NOT_FOUND);
        }

        $cliente->delete();

        return response()->json(['id' => $request->id], Response::HTTP_OK);
    }
}
