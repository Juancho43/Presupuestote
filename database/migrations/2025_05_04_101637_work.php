
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
            $table->date('dead_line');
            $table->decimal('cost', 10, 2);
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['Presupuestado', 'Pendiente de aprobación', 'Aprobado', 'En proceso', 'Entregado', 'Cancelado']);
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

