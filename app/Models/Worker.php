<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Casts\DateCast;

class Worker extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'workers';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name', 
        'role',
    ];

    // RELATIONS
    public function tasks() : BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'task_workers');
    }
}
