<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesModel extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'sale_number',
        'client',
        'product_id',
        'qty',
        'price',
        'total'
    ];
}
