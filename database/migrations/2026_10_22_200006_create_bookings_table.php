<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue');
            $table->string('barangay')->nullable();
            $table->unsignedInteger('pax');
            $table->boolean('needs_decoration')->default(false);
            $table->unsignedSmallInteger('serving_count')->default(0);
            $table->unsignedSmallInteger('dishwashing_count')->default(0);
            $table->text('special_requests')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('reservation_fee', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['Pending', 'Confirmed', 'Completed', 'Cancelled'])->default('Pending');
            $table->enum('payment_status', ['Unpaid', 'Partial', 'Paid'])->default('Unpaid');
            $table->timestamps();

            $table->index(['vendor_id', 'event_date']);
            $table->index(['customer_id', 'status']);
        });

        Schema::create('booking_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['booking_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_staff');
        Schema::dropIfExists('bookings');
    }
};
