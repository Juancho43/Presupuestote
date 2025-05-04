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
    Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        $table->decimal('stock', 10, 2);
        $table->date('date');
        $table->foreignId('material_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        $table->softDeletes();
    });
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
    Schema::dropIfExists('stocks');
}



};
