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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->enum('type', ['purchase', 'sale']); // pembelian atau penjualan
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('transaction_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('deposit', 15, 2)->default(0); // deposit/uang muka
            $table->decimal('final_amount', 15, 2)->default(0); // total setelah deposit
            $table->text('notes')->nullable();
            $table->enum('payment_method', ['cash', 'transfer', 'credit'])->nullable();
            $table->text('payment_notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
