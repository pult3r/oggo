<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Casts\DateCast;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'id',
        'name', 
        'description', 
        'startdate', 
        'enddate', 
    ];

    protected $casts = [
        'id' => 'string',
        'startdate' => DateCast::class,
        'enddate' => DateCast::class,
    ];
    
    protected $keyType = 'string';

    public function tasks(){        
        //return $this->hasMany(Task::class);
    }
}
