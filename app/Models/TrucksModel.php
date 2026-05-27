<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrucksModel extends Model
{
    use HasFactory;

    protected $table = 'trucks';

    protected $fillable = [
        'truck',
        'license_plate',
        'capacity',
        'status'
    ];
}
