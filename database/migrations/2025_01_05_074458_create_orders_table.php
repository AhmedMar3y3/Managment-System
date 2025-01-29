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
            $table->enum('order_type', ['كيك','ورد', 'كيك و ورد'])->default('كيك');
            $table->text('order_details')->nullable();
            $table->foreignId('flower_id')->nullable()->constrained('flowers')->onDelete('cascade');
            $table->integer('flower_quantity')->default(0);
            $table->string('image')->nullable();
            $table->integer('quantity')->default(0);
            $table->time('delivery_time')->nullable();
            $table->date('delivery_date')->nullable();
            // second screen
            $table->double('price')->nullable();
            $table->double('flower_price')->nullable();
            $table->double('delivery_price')->nullable();
            $table->double('total_price')->nullable();
            $table->double('deposit')->default(0);
            $table->double('remaining')->nullable();
            // third screen
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('map_desc')->nullable();
            $table->text('additional_data')->nullable();

            // other way 
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            // another screens for المرتجعات
            $table->boolean('is_returned')->default(false);
            $table->text('problem')->nullable(); 
            // another data
            $table->enum('status', ["جاري الاستلام","وافق المدير","تم القبول","قيد التنفيذ", "تم التجهيز","استلام السائق","رفض السائق","مرتجع", "تم التوصيل"])->default("جاري الاستلام");
            $table->enum('payment_method', ["cash","visa"])->default('cash');
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
