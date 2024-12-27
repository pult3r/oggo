<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\Worker;
use App\Models\TaskWorker;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::factory()->count(5)->create()
            ->each(function (Project $Project) {
                $Project->tasks()->saveMany(
                    Task::factory()
                        ->count( rand(1,5) )
                        ->make()
                ); 
        });

        Worker::factory()->count(20)->create();

        $Tasks = Task::get();

        foreach($Tasks as $Task){
            $Workers = Worker::inRandomOrder()->limit( rand(1,5) )->get();
            foreach($Workers as $Worker){
                TaskWorker::create([
                    'task_id' => $Task->id,
                    'worker_id' => $Worker->id
                ]);
            }
        }
    }
}
