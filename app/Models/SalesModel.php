<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesModel extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'client_id',
        'product_id',
        'qty',
        'price',
        'date',
        'company_id'
    ];
}
