<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Casts\DateCast;
use App\Filament\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Project;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'tasks';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'project_id',
        'name', 
        'description', 
        'startdate', 
        'enddate', 
        'status', 
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
        'status' =>  TaskStatus::class,
    ];

    // RELATIONS
    public function project() : BelongsTo
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    public function workers() : BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'task_workers');
    }
}
