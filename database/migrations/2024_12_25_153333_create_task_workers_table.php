<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Task;
use App\Models\Worker;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('task_id')->references('id')->on('tasks'); 
            $table->foreignUuid('worker_id')->references('id')->on('workers'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_workers');
    }
};
