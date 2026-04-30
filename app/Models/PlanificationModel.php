<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanificationModel extends Model
{
    use HasFactory;

    protected $table = 'planification';
    protected $primaryKey = 'pid';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'product_id',
        'planned_qty',
        'start_date',
        'end_date',
        'status',
        'company_id',
    ];
}