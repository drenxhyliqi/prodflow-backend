<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacationsModel extends Model
{
    protected $table = 'vacations';
    protected $primaryKey = 'vid';
    public $timestamps = false;
    protected $fillable = [
        'staff_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'company_id',
    ];
}
