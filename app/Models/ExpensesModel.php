<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensesModel extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'comment',
        'price',
        'date',
        'company_id'
    ];
}
