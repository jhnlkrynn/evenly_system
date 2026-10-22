<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();
            // Plain text (not a barangays FK): listings also use "Poblacion", which isn't one of the 44 official names.
            $table->string('barangay')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
