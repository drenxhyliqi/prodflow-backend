<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialsModel extends Model
{
    use HasFactory;

    protected $table = 'materials';
    protected $primaryKey = 'mid';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'material',
        'unit',
        'company_id',
    ];
}
