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
             Schema::create('employees', function (Blueprint $table) {
                 $table->id();
                 $table->decimal('salary', 10, 2)->nullable();
                 $table->datetime('start_date')->nullable();
                 $table->datetime('end_date')->nullable();
                 $table->boolean('is_active')->default(true);
                 $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
                 $table->timestamps();
                 $table->softDeletes();
             });
         }

         /**
          * Reverse the migrations.
          */
         public function down(): void
         {
             Schema::dropIfExists('employees');
         }
     };
