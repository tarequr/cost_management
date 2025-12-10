
<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateFinalBudgetsTable extends Migration
    {
        public function up()
        {
            Schema::create('final_budgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->onDelete('cascade');
                $table->decimal('PV', 15, 2);
                $table->decimal('AC', 15, 2);
                $table->decimal('EV', 15, 2);
                $table->decimal('SPI', 8, 2);
                $table->decimal('CPI', 8, 2);
                $table->decimal('CV', 15, 2);
                $table->decimal('SV', 15, 2);
                $table->decimal('BAC', 15, 2);
                $table->decimal('ETC', 15, 2);
                $table->decimal('EAC', 15, 2);
                $table->timestamps();
            });
        }

        public function down()
        {
            Schema::dropIfExists('final_budgets');
        }
}
