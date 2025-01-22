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
            // first screen
            $table->id();
            $table->enum('order_type', ['كيك','ورد'])->default('كيك');
            $table->text('order_details');
            $table->integer('quantity')->default(1);
            $table->date('delivery_date');
            // second screen
            $table->double('price')->nullable();
            $table->double('deposit')->default(0);
            $table->double('remaining')->nullable();
            // third screen
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('map_desc')->nullable();
            $table->text('additional_data')->nullable();
            // another screens for المرتجعات
            $table->boolean('is_returned')->default(false);
            $table->text('problem')->nullable();
            // another data
            $table->enum('status', ["جاري الاستلام","وافق المدير","تم القبول","تم الرفض","قيد التنفيذ", "تم التجهيز","استلام السائق","رفض السائق","مرتجع", "تم التوصيل"])->default("جاري الاستلام");
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
