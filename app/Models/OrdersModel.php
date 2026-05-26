<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersModel extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $primaryKey = 'oid';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'order_number',
        'client',
        'product_id',
        'qty',
        'price',
        'total',
        'status',
        'sale_number',
        'date',
        'company_id',
    ];
}
