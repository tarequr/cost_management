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
        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('amount', 'cost');
        });

        Schema::table('draft_budgets', function (Blueprint $table) {
            $table->renameColumn('total_amount', 'total_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('cost', 'amount');
        });

        Schema::table('draft_budgets', function (Blueprint $table) {
            $table->renameColumn('total_cost', 'total_amount');
        });
    }
};
