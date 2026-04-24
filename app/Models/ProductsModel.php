<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsModel extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $primaryKey = 'pid';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'product',
        'unit',
        'price',
        'company_id',
    ];
}
