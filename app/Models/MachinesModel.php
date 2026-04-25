<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachinesModel extends Model
{
    use HasFactory;

    protected $table = 'machines';
    protected $primaryKey = 'mid'; 

    public $timestamps = false;

    protected $fillable = [
        'machine',
        'type',
        'company_id'
    ];
}