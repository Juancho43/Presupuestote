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
    Schema::create('people', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('last_name')->nullable();
    $table->string('address')->nullable();
    $table->string('phone_number');
    $table->string('mail')->unique()->nullable();
    $table->string('dni')->unique()->nullable();
    $table->string('cuit')->unique()->nullable();
    $table->timestamps();
    $table->softDeletes();
});
}

/**
* Reverse the migrations.
*/
public function down(): void
{
Schema::dropIfExists('people');
}
};
