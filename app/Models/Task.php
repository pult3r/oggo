<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Casts\DateCast;
use App\Models\Project;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'task_id',
        'name', 
        'description', 
        'startdate', 
        'enddate', 
        'status', 
        'user_id', 
    ];

    protected $casts = [
        'id' => 'string',
        'startdate' => DateCast::class,
        'enddate' => DateCast::class,
    ];


    // RELATIONS
    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }

}
