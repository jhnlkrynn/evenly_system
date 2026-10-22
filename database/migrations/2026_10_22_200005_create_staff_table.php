<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            // Null = Eventlynk's own crew, managed from the admin panel and shared across bookings.
            $table->foreignId('vendor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->enum('role', ['Decoration', 'Serving', 'Dishwashing']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
