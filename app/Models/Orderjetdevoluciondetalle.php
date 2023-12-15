<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Orderjetdevoluciondetalle.
 *
 * @property int $id
 * @property int $idOrdenDevolucion
 * @property string $order_item_id
 * @property string $alt_order_item_id
 * @property int $return_quantity
 * @property string $merchant_sku
 * @property string $merchant_sku_title
 * @property string $reason
 * @property string $requested_refund_amount
 * @property float $amount_principal
 * @property float $amoun_tax
 * @property float $amount_shipping_cost
 * @property float $amount_shipping_tax
 */
class Orderjetdevoluciondetalle extends Model
{
    protected $table = 'orderjetdevoluciondetalle';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'idOrdenDevolucion',
        'order_item_id',
        'alt_order_item_id',
        'return_quantity',
        'merchant_sku',
        'merchant_sku_title',
        'reason',
        'requested_refund_amount',
        'amount_principal',
        'amoun_tax',
        'amount_shipping_cost',
        'amount_shipping_tax',
    ];
}
