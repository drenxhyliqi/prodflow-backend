<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialsStockModel extends Model
{
    use HasFactory;

    protected $table = 'materials_stock';

    protected $primaryKey = 'msid';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'material_id',
        'type',
        'qty',
        'date',
        'warehouse_id',
        'company_id',
    ];
}
