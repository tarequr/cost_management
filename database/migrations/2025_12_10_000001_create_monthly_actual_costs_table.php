<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_actual_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('month'); // Format: 'YYYY-MM' (e.g., '2025-01')
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->decimal('earned_value_percentage', 5, 2)->default(0); // 0-100
            $table->timestamps();
            $table->unique(['task_id', 'month']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_actual_costs');
    }
};
