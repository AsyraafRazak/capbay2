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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email')->index();
            $table->string('customer_phone');
            $table->string('car_model')->default('CapBay Vroom');
            $table->unsignedBigInteger('price_cents')->default(20000000); // RM 200,000.00
            $table->unsignedBigInteger('down_payment_paid_cents')->default(0);
            $table->boolean('loan_approved')->default(false);
            $table->string('status')->default('registered')->index();
            $table->timestamps();
            
            // Add composite or extra indexes for performant lookups at 50,000 scale
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
