<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Orderjet.
 *
 * @property int $id
 * @property string|null $merchant_order_id
 * @property string|null $reference_order_id
 * @property string|null $status
 * @property string|null $hash_email
 * @property int|null $idPedido
 * @property string|null $detalle
 * @property Carbon|null $fecha
 * @property string|null $enlace
 * @property Carbon|null $response_shipment_date
 * @property string|null $response_shipment_method
 * @property Carbon|null $expected_delivery_date
 * @property string|null $ship_from_zip_code
 * @property Carbon|null $carrier_pick_up_date
 * @property string|null $carrier
 * @property string|null $enlaceDevolucion
 */
class Orderjet extends Model
{
    protected $table = 'orderjet';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'merchant_order_id',
        'reference_order_id',
        'status',
        'hash_email',
        'idPedido',
        'detalle',
        'fecha',
        'enlace',
        'response_shipment_date',
        'response_shipment_method',
        'expected_delivery_date',
        'ship_from_zip_code',
        'carrier_pick_up_date',
        'carrier',
        'enlaceDevolucion',
    ];
}
