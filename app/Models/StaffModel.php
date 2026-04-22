<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffModel extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $primaryKey = 'sid';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'surname',
        'position',
        'contact',
        'company_id',
    ];
}
