<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Orderjetdevolucion.
 *
 * @property int $id
 * @property string $enlace
 * @property string $detalle
 * @property bool $agree_to_return_charge
 * @property string $alt_order_id
 * @property string $alt_return_authorization_id
 * @property string $merchant_order_id
 * @property string|null $merchant_return_authorization_id
 * @property string|null $merchant_return_charge
 * @property string|null $reference_order_id
 * @property string|null $reference_return_authorization_id
 * @property bool|null $refund_without_return
 * @property string|null $return_date
 * @property string|null $return_status
 * @property string|null $shipping_carrier
 * @property string|null $tracking_number
 */
class Orderjetdevolucion extends Model
{
    protected $table = 'orderjetdevolucion';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'agree_to_return_charge' => 'bool',
        'refund_without_return' => 'bool',
    ];

    protected $fillable = [
        'id',
        'enlace',
        'detalle',
        'agree_to_return_charge',
        'alt_order_id',
        'alt_return_authorization_id',
        'merchant_order_id',
        'merchant_return_authorization_id',
        'merchant_return_charge',
        'reference_order_id',
        'reference_return_authorization_id',
        'refund_without_return',
        'return_date',
        'return_status',
        'shipping_carrier',
        'tracking_number',
    ];
}
