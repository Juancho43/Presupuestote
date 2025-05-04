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
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->date('made_date');
                $table->text('description');
                $table->date('dead_line');
                $table->enum('status', ['pending', 'approved', 'rejected']);
                $table->decimal('cost', 10, 2);
                $table->foreignId('client_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('budgets');
        }
    };
