
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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->integer('order');
            $table->string('name');
            $table->string('notes')->nullable();
            $table->integer('estimated_time');
            $table->date('dead_line')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->string('state')->default('Presupuestado');
            $table->timestamps();
            $table->softDeletes();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};

