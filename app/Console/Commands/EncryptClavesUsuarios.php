<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EncryptClavesUsuarios extends Command
{
    protected $signature = 'encrypt:claves_usuarios';
    protected $description = 'Encripta las claves de la tabla usuarios utilizando Hash::make()';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $usuarios = DB::table('usuario')->get();

        foreach ($usuarios as $usuario) {
            $hashedClave = Hash::make($usuario->clave);

            DB::table('usuario')
                ->where('id', $usuario->id)
                ->update(['clave' => $hashedClave, 'permisos' => 2]);
        }

        $this->info('¡Se han encriptado las claves de usuarios!');
    }
}
