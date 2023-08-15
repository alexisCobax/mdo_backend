<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\TokenExpiredException;
use App\Exceptions\TokenInvalidException;
use App\Exceptions\TokenNotParsedException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function register(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'nombre' => 'required',
                    'clave' => 'required'
                ]
            );

            $user = Usuario::where('nombre', $request->nombre)->first();
            if($user) {
                return response()->json([
                    'status' => false,
                    'message' => 'El nombre de usuario ya existe en el sistema',
                    'errors' => $validateUser->errors()
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = Usuario::create([
                'nombre' => $request->nombre,
                'permisos' => 1,
                'clave' => Hash::make($request->clave),
                'suspendido' => 0
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Usuario creado correctamente!',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Login The User
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
                    'clave' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
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

            return response()->json([
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'user' => [
                    "nombre" => $user->nombre,
                    "permiso" => $user->permisos
                ],

            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
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
            auth()->user()->tokens()->delete();
            return [
                'message' => 'user logged out'
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

        return response()->json(['user' => $user], 200);
    }
}
