<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoicedetalle.
 *
 * @property int $id
 * @property int $qordered
 * @property int $qshipped
 * @property int $qborder
 * @property string $itemNumber
 * @property string|null $Descripcion
 * @property float|null $listPrice
 * @property float|null $netPrice
 * @property int $invoice
 */
class Invoicedetalle extends Model
{
    protected $table = 'invoicedetalle';
    public $timestamps = false;

    protected $fillable = [
        'qordered',
        'qshipped',
        'qborder',
        'itemNumber',
        'Descripcion',
        'listPrice',
        'netPrice',
        'invoice',
    ];
}
