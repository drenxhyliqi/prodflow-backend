<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancesModel extends Model
{
    use HasFactory;

    protected $table = 'maintenances';
    protected $primaryKey = 'mid';

    protected $fillable = [
        'machine_id',
        'date',
        'description',
        'company_id'
    ];
}