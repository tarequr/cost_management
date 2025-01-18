<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget_calculators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_estimate_id')->constrained('budget_estimates')->onDelete('cascade');
            $table->string('task_name')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->enum('rate', ['fixed', 'hourly'])->nullable();
            $table->integer('fixed_rate')->nullable();
            $table->integer('hourly_rate')->nullable();
            $table->integer('number_of_hours')->nullable();
            $table->integer('total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budget_calculators');
    }
};
