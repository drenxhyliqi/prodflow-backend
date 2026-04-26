<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionModel extends Model
{
    use HasFactory;

    protected $table = 'production';

    protected $primaryKey = 'pid';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'product_id',
        'machine_id',
        'qty',
        'date',
        'company_id',
    ];
}
