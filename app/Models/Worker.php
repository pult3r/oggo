<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Casts\DateCast;

class Worker extends Model
{
    use HasFactory;

    protected $table = 'workers';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name', 
    ];
}
