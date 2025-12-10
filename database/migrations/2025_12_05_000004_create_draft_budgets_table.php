
<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateDraftBudgetsTable extends Migration
    {
        public function up()
        {
            Schema::create('draft_budgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->onDelete('cascade');
                $table->json('monthly_breakdown'); // {month: amount}
                $table->decimal('total_amount', 15, 2);
                $table->integer('total_duration');
                $table->timestamps();
            });
        }

        public function down()
        {
            Schema::dropIfExists('draft_budgets');
        }
}
