<?php

namespace App\Http\Controllers\Auth;

use Error;
use Exception;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Mail\EnvioMailCambiarClave;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Exceptions\TokenExpiredException;
use App\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\TokenNotParsedException;
use Symfony\Component\HttpFoundation\Response;

class AuthVendedorController extends Controller
{
    /**
     * Create User.
     * @param Request $request
     * @return User
     */
    // public function register(Request $request)
    // {
    //     try {
    //         Validated
    //         $validateUser = Validator::make(
    //             $request->all(),
    //             [
    //                 'nombre' => 'required',
    //                 'clave' => 'required',
    //             ]
    //         );

    //         $user = Usuario::where('nombre', $request->nombre)->first();
    //         if ($user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'El nombre de usuario ya existe en el sistema',
    //                 'errors' => $validateUser->errors(),
    //             ], Response::HTTP_UNAUTHORIZED);
    //         }

    //         if ($validateUser->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'validation error',
    //                 'errors' => $validateUser->errors(),
    //             ], Response::HTTP_UNAUTHORIZED);
    //         }

    //         $user = Usuario::create([
    //             'nombre' => $request->nombre,
    //             'permisos' => 2,
    //             'clave' => Hash::make($request->clave),
    //             'suspendido' => 0,
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Usuario creado correctamente!',
    //             'token' => $user->createToken('API TOKEN')->plainTextToken,
    //         ], Response::HTTP_OK);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage(),
    //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

    /**
     * Login The User.
     * @param Request $request
     * @return User
     */
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required',
                    'clave' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ], Response::HTTP_UNAUTHORIZED);
            }

            $credentials = $request->only('nombre', 'clave');
            if (!$this->attemp($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario y Clave son incorrectos.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = Usuario::where('nombre', $request->nombre)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario inexistente.',
                ], Response::HTTP_NOT_FOUND);
            }

            $userPermiso = Usuario::where('nombre', $request->nombre)
                      ->where('permisos', 3)
                      ->first();
            if (!$userPermiso) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autorizado.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // $client = Cliente::where('usuario', $user->id)->first();
            // if (is_object($client) && $client->prospecto == 1) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Prospecto, debe pedir autorizacion.',
            //     ], Response::HTTP_UNAUTHORIZED);
            // }

            return response()->json([
                'token' => $user->createToken('API TOKEN')->plainTextToken,
                'user' => [
                    'nombre' => $user->nombre,
                    'permiso' => $user->permisos,
                    'id' => $user->id
                ],

            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function attemp($credentials)
    {
        try {
            $usuario = Usuario::where('nombre', $credentials['nombre'])->first();

            return Hash::check($credentials['clave'], $usuario->clave);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function logout()
    {
        try {
            //auth()->user()->tokens()->delete();

            if (auth()->check()) {
                auth()->user()->tokens()->delete();
            }

            return [
                'message' => 'user logged out',
            ];
        } catch (TokenInvalidException $e) {
            return Response::json(['error' => 'Invalid token', 'code' => 401], 401);
        } catch (TokenExpiredException $e) {
            return Response::json(['error' => 'Token has Expired', 'code' => 401], 401);
        } catch (TokenNotParsedException $e) {
            return Response::json(['error' => 'Token not parsed', 'code' => 401], 401);
        }
    }

    public function me()
    {

        $user = Auth::user();
        unset($user->clave);
        unset($user->token_exp);
        unset($user->apellido);

        return response()->json(['user' => $user], 200);
    }

    public function rescue(Request $request)
    {

        $cliente = Cliente::where('email', $request->email)->first();
        if (!$cliente) {
            return response()->json([
                'status' => false,
                'message' => 'Cliente inexistente.',
            ], Response::HTTP_NOT_FOUND);
        } else {
            $user = Usuario::where('id', $cliente->usuario)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cliente sin usuario.',
                ], Response::HTTP_NOT_FOUND);
            } else {
                if ($request->token == '$2a$12$273kc01bpP4ZOL.4XE/6jeGOGtg397AVboF.WXxKG2Qk0EQA0H9Xm') {
                    $user->clave = Hash::make($request->clave);
                    $user->save();

                    return response()->json([
                        'status' => 200,
                        'usuario' => $user->nombre,
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Token invalido.',
                    ], Response::HTTP_UNAUTHORIZED);
                }
            }

            return response()->json([
                'user' => [
                    'nombre' => $user->nombre,
                    'permiso' => $user->permisos,
                ],

            ], Response::HTTP_OK);
        }

        $user = Auth::user();
        unset($user->clave);
        unset($user->token_exp);
        unset($user->apellido);

        return response()->json(['user' => $user], 200);
    }

    public function refresh(Request $request)
    {

        try {
            $user = Auth::user();
            $user = Usuario::where('id', $user->id)->first();

            $user->clave = Hash::make($request->clave);

            $user->save();

            // /* Envio un Email **/

            // try {
            //     $cuerpo = 'mdo.emailCambiarClave';
            //     $subject = 'Cambio de clave';
            //     $nombre = 'Cambio de clave';

            //     $destinatarios = [
            //         'alexiscobax1@gmail.com',
            //     ];

            //     Mail::to($destinatarios)->send(new EnvioMailCambiarClave($cuerpo, $subject, $nombre));

            //     return response()->json(['response' => 'Su clave ha sido cambiada con exito!'], 200);
            // } catch (Exception $e) {
            //     return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            // }
            return response()->json(['response' => 'Su clave ha sido cambiada con exito!', 'status'=>200], 200);   
        } catch (\Exception $e) {
            return response()->json(['error'], $e->getMessage());
        }
    }

    function generarAlfanumerico($longitud = 10) {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $resultado = '';
        $max = strlen($caracteres) - 1;

        for ($i = 0; $i < $longitud; $i++) {
            $resultado .= $caracteres[random_int(0, $max)];
        }

        return $resultado;
    }

    public function recuperar(Request $request)
    {

        try{

            $clave_temporal = $this->generarAlfanumerico(10);

            $clave = Hash::make($clave_temporal);

            Usuario::where('nombre', $request->email)
       ->update(['clave' => $clave]);

    //         Usuario::where('nombre', 'like', '%' . $request->email . '%')
    //    ->update(['clave' => $clave]);


                    /* Envio email a cliente **/
        try {        

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://services.leadconnectorhq.com/hooks/40UecLU7dZ4KdLepJ7UR/webhook-trigger/EaO0YMcQbDbGG6Ma7iXc',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_POSTFIELDS =>'{
              "email":"'.$request->email.'",
              "usuario":"'.$request->email.'",
              "clave":"'.$clave_temporal.'"
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

        }catch(Error $e){
            return response()->json(['error' => $e->getMessage()], 200);
        }

    }

    public function forceLogoutUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = \App\Models\Usuario::where('nombre', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Eliminar todos los tokens -> se desloguea en todos lados
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => "El usuario {$user->nombre} fue deslogueado correctamente"
        ], 200);
    }


}
