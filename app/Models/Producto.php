<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Producto.
 *
 * @property int $id
 * @property string $nombre
 * @property string $descripcion
 * @property int $tipo
 * @property int $categoria
 * @property int $marca
 * @property int $material
 * @property int $estuche
 * @property int $sexo
 * @property int $proveedor
 * @property float $precio
 * @property bool $suspendido
 * @property int $comision
 * @property int $stock
 * @property int $stockMinimo
 * @property string|null $codigo
 * @property bool $alarmaStockMinimo
 * @property string $color
 * @property int $tamano
 * @property string $ubicacion
 * @property int|null $grupo
 * @property bool $pagina
 * @property float $costo
 * @property bool $bestBrasil
 * @property int $posicion
 * @property int|null $stockRoto
 * @property Carbon|null $ultimoIngresoDeMercaderia
 * @property Carbon|null $ultimaVentaDeMercaderia
 * @property int|null $genero
 * @property int|null $imagenPrincipal
 * @property string|null $UPCreal
 * @property bool|null $mdoNet
 * @property bool|null $jet
 * @property float|null $precioJet
 * @property int|null $stockJet
 * @property int|null $multipack
 * @property string|null $nodeJet
 * @property string|null $nombreEN
 * @property string|null $descripcionEN
 * @property float|null $peso
 * @property bool|null $enviadoAJet
 * @property int|null $stockFalabella
 * @property float|null $precioFalabella
 * @property bool|null $verEnFalabella
 * @property bool|null $enviadoAFalabella
 * @property int|null $categoriaFalabella
 * @property string|null $marcaFalabella
 * @property string|null $descripcionFalabella
 * @property float $precioPromocional
 * @property bool $destacado
 * @property float $largo
 * @property float $alto
 * @property float $ancho
 * @property string $descripcionLarga
 * @property int $colorPrincipal
 * @property int $colorSecundario
 */
class Producto extends Model
{
    protected $table = 'producto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'categoria',
        'marca',
        'material',
        'estuche',
        'sexo',
        'proveedor',
        'precio',
        'suspendido',
        'comision',
        'stock',
        'stockMinimo',
        'codigo',
        'alarmaStockMinimo',
        'color',
        'tamano',
        'ubicacion',
        'grupo',
        'pagina',
        'costo',
        'bestBrasil',
        'posicion',
        'stockRoto',
        'ultimoIngresoDeMercaderia',
        'ultimaVentaDeMercaderia',
        'genero',
        'imagenPrincipal',
        'UPCreal',
        'mdoNet',
        'jet',
        'precioJet',
        'stockJet',
        'multipack',
        'nodeJet',
        'nombreEN',
        'descripcionEN',
        'peso',
        'enviadoAJet',
        'stockFalabella',
        'precioFalabella',
        'verEnFalabella',
        'enviadoAFalabella',
        'categoriaFalabella',
        'marcaFalabella',
        'descripcionFalabella',
        'precioPromocional',
        'destacado',
        'largo',
        'alto',
        'ancho',
        'descripcionLarga',
        'colorPrincipal',
        'colorSecundario',
        'nuevo',
    ];

    //Relationships

    public function categorias()
    {
        return $this->belongsTo(Categoriaproducto::class, 'categoria');
    }

    public function marcas()
    {
        return $this->belongsTo(Marcaproducto::class, 'marca');
    }

    public function colores()
    {
        return $this->belongsTo(Color::class, 'color');
    }

    public function fotos()
    {
        return $this->hasMany(Fotoproducto::class, 'idProducto');
    }

    public function materiales()
    {
        return $this->belongsTo(Materialproducto::class, 'material');
    }

    public function tamanos()
    {
        return $this->belongsTo(Tamanoproducto::class, 'tamano');
    }

    //Filters

    public function scopeCodigo($query, $codigo)
    {
        if ($codigo) {
            return $query->where('codigo', $codigo)->orWhere('id', $codigo);
        }

        return $query;
    }

    public function scopeCategoria($query, $categoria)
    {
        if ($categoria) {
            return $query->where('categoria', $categoria);
        }
    }

    public function scopeNombre($query, $nombre)
    {
        if ($nombre) {
            return $query->whereRaw('LOWER(producto.nombre) LIKE ?', ['%' . strtolower($nombre) . '%'])
                ->orWhereRaw('LOWER(codigo) LIKE ?', ['%' . strtolower($nombre) . '%']);
        }
    }

    public function scopeNombreMarca($query, $nombreMarca)
    {
        if ($nombreMarca) {
            $marcas = Marcaproducto::whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($nombreMarca) . '%'])->get();
            $marcaIds = $marcas->pluck('id')->toArray();
            return $query->whereIn('marca', $marcaIds);
        }
    }

    public function scopeSuspendido($query, $suspendido)
    {
        if ($suspendido) {
            return $query->where('suspendido', '=', $suspendido);
        }
    }

    public function scopeTipo($query, $tipo)
    {
        if ($tipo) {
            return $query->where('tipo', '=', $tipo);
        }
    }

    public function scopeIdMarca($query, $idMarca)
    {
        if ($idMarca) {
            return $query->where('marca', '=', $idMarca);
        }
    }

    public function scopeBuscador($query, $buscador)
    {
        if ($buscador) {
            return $query->where(function ($query) use ($buscador) {
                $query->where('descripcion', 'LIKE', "%$buscador%")
                    ->orWhere('tamano', 'LIKE', "%$buscador%")
                    ->orWhere('nombre', 'LIKE', "%$buscador%")
                    ->orWhere('codigo', 'LIKE', "%$buscador%")
                    ->orWhereHas('marcaBuscador', function ($query) use ($buscador) {
                        $query->where('nombre', 'LIKE', "%$buscador%");
                    })
                    ->orWhereHas('colorBuscador', function ($query) use ($buscador) {
                        $query->where('nombre', 'LIKE', "%$buscador%");
                    });
            })
                ->where('stock', '>', 0);
        }
    }

    public function scopeMaterial($query, $material)
    {
        if ($material) {
            return $query->where('material', '=', $material);
        }
    }

    public function scopeColor($query, $color)
    {
        if ($color) {
            return $query->whereRaw('LOWER(color) LIKE ?', ['%' . strtolower($color) . '%']);
        }
    }

    public function scopeGrupo($query, $grupo)
    {
        if ($grupo) {
            return $query->where('grupo', '=', $grupo);
        }
    }

    public function scopePrecioRange($query, $precioDesde, $precioHasta)
    {
        if ($precioDesde !== null && $precioHasta !== null) {
            return $query->whereBetween('precio', [$precioDesde, $precioHasta]);
        } elseif ($precioDesde !== null) {
            return $query->where('precio', '>=', $precioDesde);
        } elseif ($precioHasta !== null) {
            return $query->where('precio', '<=', $precioHasta);
        }
    }

    public function scopeStockRange($query, $stockDesde, $stockHasta)
    {
        if ($stockDesde !== null && $stockHasta !== null) {
            return $query->whereBetween('stock', [$stockDesde, $stockHasta]);
        } elseif ($stockDesde !== null) {
            return $query->where('stock', '>=', $stockDesde);
        } elseif ($stockHasta !== null) {
            return $query->where('stock', '<=', $stockHasta);
        }
    }

    public function scopeDestacado($query, $destacado)
    {
        if ($destacado) {
            return $query->where('destacado', '=', $destacado);
        }
    }

    public function scopeNuevosProductos($query, $estado)
    {
        if ($estado == 'nuevo') {
            return $query->where('nuevo', '=', 1)
                //->orderBy('id', 'desc')
                ->take(3);
        }
    }

    public function scopePrecioPromocional($query)
    {
        $query->where('precioPromocional', '!=', 0);
    }

    public function scopeMenos20($query)
    {
        $query->where(function($query) {
            $query->where('precioPromocional', '=', 0)
                  ->where('precio', '<=', 20.00);
        })
        ->orWhere(function($query) {
            $query->where('precioPromocional', '>', 0)
                  ->where('precioPromocional', '<=', 20.00);
        });
    }

    public function scopeMarcaBuscador()
    {
        return $this->belongsTo(Marcaproducto::class, 'marca');
    }

    public function scopeColorBuscador()
    {
        return $this->belongsTo(Color::class, 'color');
    }

    public function scopeRebajados($query)
{
    $query->where(function ($query) {
        $query->where('precioPromocional', '>', 0)
              ->orWhere(function ($query) {
                  $query->where('precioPromocional', '<=', 9.99)
                        ->orWhereBetween('precio', [0, 9.99]);
              });
    });
}


    // public function scopeRebajados($query)
    // {
    //     $query->where('precioPromocional', '<=', 9.99);
    // }
}
