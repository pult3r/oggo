<?php

namespace App\Models;

use App\Models\Task;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskWorker extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    
    protected $table = 'task_workers';
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'task_id',
        'worker_id', 
    ];

    // RELATIOPNS 
    public function workers() 
    {
        return $this->belongsTo(Worker::class,'worker_id');
    }

    public function tasks()
    {
        return $this->belongsTo(Task::class,'task_id');
    }
}
