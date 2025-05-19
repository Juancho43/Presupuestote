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
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->date('date');
                $table->decimal('total', 10, 2)->default(0);
                $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
                $table->string('payment_status')->default('Deuda');
                $table->timestamps();
                $table->softDeletes();
            });


        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {

            Schema::dropIfExists('invoices');
        }
    };
