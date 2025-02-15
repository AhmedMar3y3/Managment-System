<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->text('order_details')->nullable();
            //flowers
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            
            //any order
            $table->boolean('is_sameday')->default(false);
            $table->time('delivery_time')->nullable();
            $table->date('delivery_date')->nullable();
            $table->enum('order_type', ['cake','flower', 'cake and flower'])->default('cake');

            // second screen
            $table->double('cake_price')->default(0);
            $table->double('flower_price')->default(0);
            $table->double('delivery_price')->default(0);
            $table->double('deposit')->default(0);
            $table->double('total_price')->default(0);

            // third screen
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('map_desc')->nullable();
            $table->text('additional_data')->nullable();

            // another screens for returned orders
            $table->boolean('is_returned')->default(false);
            $table->text('problem')->nullable();
            
            // when a driver decline order
            $table->text('rejection_cause')->nullable();

            // another data
            $table->enum('status', ["new","manager accepted","chef waiting","chef approved","inprogress", "completed","delivery waiting","delivery recieved","delivery declined", "start ride","returned", "delivered"])->default("new");
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
