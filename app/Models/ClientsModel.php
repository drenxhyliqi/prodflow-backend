<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientsModel extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'client',
        'phone',
        'location',
        'company_id'
    ];
}
