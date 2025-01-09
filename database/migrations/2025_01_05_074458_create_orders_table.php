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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_address');
            $table->string('order_type')->nullable();
            $table->text('order_details');
            $table->enum('status', ["جاري الاستلام","وافق المدير","تم القبول","تم الرفض","قيد التنفيذ", "تم التجهيز", "تم التوصيل"])->default("جاري الاستلام");
            $table->double('price');
            $table->double('deposit')->default(0);
            $table->date('delivery_date');
            $table->text('notes')->nullable();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('cascade');
            $table->foreignId('manager_id')->nullable()->constrained('managers')->onDelete('cascade');
            $table->foreignId('chef_id')->nullable()->constrained('chefs')->onDelete('cascade');
            $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
