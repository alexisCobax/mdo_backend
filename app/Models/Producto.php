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

    protected $casts = [
        'tipo' => 'int',
        'categoria' => 'int',
        'marca' => 'int',
        'material' => 'int',
        'estuche' => 'int',
        'sexo' => 'int',
        'proveedor' => 'int',
        'precio' => 'float',
        'suspendido' => 'bool',
        'comision' => 'int',
        'stock' => 'int',
        'stockMinimo' => 'int',
        'alarmaStockMinimo' => 'bool',
        'tamano' => 'int',
        'grupo' => 'int',
        'pagina' => 'bool',
        'costo' => 'float',
        'bestBrasil' => 'bool',
        'posicion' => 'int',
        'stockRoto' => 'int',
        'ultimoIngresoDeMercaderia' => 'datetime',
        'ultimaVentaDeMercaderia' => 'datetime',
        'genero' => 'int',
        'imagenPrincipal' => 'int',
        'mdoNet' => 'bool',
        'jet' => 'bool',
        'precioJet' => 'float',
        'stockJet' => 'int',
        'multipack' => 'int',
        'peso' => 'float',
        'enviadoAJet' => 'bool',
        'stockFalabella' => 'int',
        'precioFalabella' => 'float',
        'verEnFalabella' => 'bool',
        'enviadoAFalabella' => 'bool',
        'categoriaFalabella' => 'int',
        'precioPromocional' => 'float',
        'destacado' => 'bool',
        'largo' => 'float',
        'alto' => 'float',
        'ancho' => 'float',
        'colorPrincipal' => 'int',
        'colorSecundario' => 'int',
    ];

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
            return $query->where('id', $codigo);
        }

        return $query;
    }

    public function scopeCategoria($query, $categoria)
    {
        if ($categoria) {
            return $query->where('categoria', $categoria);
        }

        return $query;
    }

    public function scopeNombre($query, $nombre)
    {
        if ($nombre) {
            return $query->where('nombre', 'LIKE', '%' . $nombre . '%');
        }

        return $query;
    }

    public function scopeSuspendido($query, $suspendido)
    {
        if ($suspendido) {
            return $query->where('suspendido', '=', $suspendido);
        }

        return $query;
    }

    public function scopeTipo($query, $tipo)
    {
        if ($tipo) {
            return $query->where('tipo', '=', $tipo);
        }

        return $query;
    }

    public function scopeMarca($query, $marca)
    {
        if ($marca) {
            return $query->where('marca', '=', $marca);
        }

        return $query;
    }

    public function scopeIdMarca($query, $idMarca)
    {
        if ($idMarca) {
            return $query->where('marca', '=', $idMarca);
        }

        return $query;
    }

    public function scopeMaterial($query, $material)
    {
        if ($material) {
            return $query->where('material', '=', $material);
        }

        return $query;
    }

    public function scopeColor($query, $color)
    {
        if ($color) {
            return $query->where('color', '=', $color);
        }

        return $query;
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

        return $query;
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

        return $query;
    }

    public function scopeDestacado($query, $destacado)
    {
        if ($destacado) {
            return $query->where('destacado', '=', $destacado);
        }

        return $query;
    }

    public function scopeNuevosProductos($query, $estado)
    {
        if ($estado == 'nuevo') {
            return $query->where('stock', '>', 0)
                ->where('precio', '>', 0)
                ->orderBy('ultimoIngresoDeMercaderia', 'desc')
                ->take(3);
        }
    }

    public function scopeMarcaBuscador()
    {
        return $this->belongsTo(Marcaproducto::class, 'marca');
    }

    public function scopeColorBuscador()
    {
        return $this->belongsTo(Color::class, 'color');
    }
}
