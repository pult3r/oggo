<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->foreignUuid('project_id')->references('id')->on('projects'); 
            $table->string('name',255)->nullable();
            $table->text('description')->nullable();
            $table->date('startdate')->nullable();
            $table->date('enddate')->nullable();
            $table->enum('status', array('todo','inprogress','done'))->default('todo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
