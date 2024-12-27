<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Casts\DateCast;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'projects';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name', 
        'description', 
        'startdate', 
        'enddate', 
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'startdate' => DateCast::class,
        'enddate' => DateCast::class,
    ];

    // RELATIONS
    
    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function workers() : HasManyThrough
    {
        return $this->hasManyThrough(TaskWorker::class, Worker::class,  'id', 'task_id');
    }    
}
