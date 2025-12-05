<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('task_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('amount', 15, 2);
            $table->integer('duration')->nullable(); // in months
            $table->timestamps();
            $table->index(['project_id', 'start_date', 'end_date']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
