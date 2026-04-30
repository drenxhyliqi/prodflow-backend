<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousesModel extends Model
{
    use HasFactory;

    protected $table = 'warehouses';
    protected $primaryKey = 'wid'; 

    protected $fillable = [
        'warehouse',
        'location',
        'company_id'
    ];
}