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
        Schema::create('managers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('image')->nullable();
            $table->rememberToken();
            $table->string('password');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->enum('status', ["قيد الانتظار", "مقبول", "مرفوض"])->default("قيد الانتظار");
            $table->string('verification_code')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
