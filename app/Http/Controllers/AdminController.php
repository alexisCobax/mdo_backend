<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        // Si ya está autenticado, redirigir al home
        if (Auth::check() && $this->isAdmin(Auth::user())) {
            return redirect()->route('admin.home');
        }
        
        return view('admin.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'clave' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $usuario = Usuario::where('nombre', $request->nombre)->first();

        if (!$usuario) {
            return back()->withErrors(['nombre' => 'Usuario no encontrado'])->withInput();
        }

        // Verificar que tenga acceso al panel (dev == 1)
        // if (!$this->isAdmin($usuario)) {
        //     return back()->withErrors(['nombre' => 'No tienes permisos de administrador'])->withInput();
        // }

        // Verificar contraseña
        if (!Hash::check($request->clave, $usuario->clave)) {
            return back()->withErrors(['clave' => 'Contraseña incorrecta'])->withInput();
        }

        // Verificar si está suspendido
        if ($usuario->suspendido) {
            return back()->withErrors(['nombre' => 'Usuario suspendido'])->withInput();
        }

        // Autenticar usuario
        Auth::login($usuario, $request->has('remember'));
        
        // Generar token Sanctum para las peticiones API
        // Primero eliminar tokens anteriores del mismo nombre para evitar acumulación
        $usuario->tokens()->where('name', 'admin-panel')->delete();
        $token = $usuario->createToken('admin-panel')->plainTextToken;
        session(['api_token' => $token]);

        return redirect()->route('admin.home');
    }

    /**
     * Mostrar home del admin
     */
    public function home()
    {
        return view('admin.home');
    }

    /**
     * Mostrar página de Nuevos Arribos
     */
    public function nuevosArribos()
    {
        // Obtener o generar token para las peticiones API
        $token = session('api_token');
        if (!$token && Auth::check()) {
            // Eliminar tokens anteriores del mismo nombre
            Auth::user()->tokens()->where('name', 'admin-panel')->delete();
            $token = Auth::user()->createToken('admin-panel')->plainTextToken;
            session(['api_token' => $token]);
        }
        
        // Si aún no hay token, algo está mal
        if (!$token) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'No se pudo generar el token de autenticación']);
        }
        
        return view('admin.nuevos-arribos', compact('token'));
    }

    /**
     * Mostrar página de Ejemplo
     */
    public function ejemplo()
    {
        // Generar datos random para ejemplo
        $arribos = $this->generateArribosData();
        
        return view('admin.ejemplo', compact('arribos'));
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        // Eliminar tokens de Sanctum del usuario
        if (Auth::check()) {
            Auth::user()->tokens()->delete();
        }
        
        // Limpiar sesión
        session()->forget('api_token');
        Auth::logout();
        
        return redirect()->route('admin.login');
    }

    /**
     * Verificar si el usuario tiene acceso al panel admin.
     * Solo usuarios con dev == 1 pueden acceder.
     */
    private function isAdmin($usuario)
    {
        return $usuario->dev == 1;
    }

    /**
     * Generar datos random para el home
     */
    private function generateRandomData()
    {
        $datos = [];
        $tipos = ['Productos', 'Ventas', 'Clientes', 'Pedidos', 'Inventario', 'Reportes'];
        
        for ($i = 0; $i < 10; $i++) {
            $datos[] = [
                'id' => rand(1000, 9999),
                'tipo' => $tipos[array_rand($tipos)],
                'nombre' => 'Item ' . ($i + 1),
                'valor' => rand(100, 10000),
                'fecha' => date('Y-m-d', strtotime('-' . rand(0, 30) . ' days')),
                'estado' => ['Activo', 'Inactivo', 'Pendiente'][array_rand(['Activo', 'Inactivo', 'Pendiente'])],
            ];
        }
        
        return $datos;
    }

    /**
     * Generar datos random para nuevos arribos
     */
    private function generateArribosData()
    {
        $arribos = [];
        $productos = ['Gafas de Sol', 'Lentes de Contacto', 'Estuches', 'Limpieza', 'Accesorios'];
        
        for ($i = 0; $i < 15; $i++) {
            $arribos[] = [
                'id' => rand(10000, 99999),
                'producto' => $productos[array_rand($productos)],
                'cantidad' => rand(10, 500),
                'proveedor' => 'Proveedor ' . rand(1, 10),
                'fecha_arribo' => date('Y-m-d', strtotime('-' . rand(0, 7) . ' days')),
                'estado' => ['Recibido', 'En Tránsito', 'Pendiente'][array_rand(['Recibido', 'En Tránsito', 'Pendiente'])],
            ];
        }
        
        return $arribos;
    }
}

