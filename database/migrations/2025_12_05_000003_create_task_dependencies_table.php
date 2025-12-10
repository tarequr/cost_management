<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('depends_on_task_id')->constrained('tasks')->onDelete('cascade');
            $table->enum('type', ['FF', 'SS'])->comment('FF=Finish-to-Finish, SS=Start-to-Start');
            $table->timestamps();
            $table->unique(['task_id', 'depends_on_task_id']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('task_dependencies');
    }
};
