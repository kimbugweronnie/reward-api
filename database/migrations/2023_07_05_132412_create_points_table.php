<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->integer('balance_points')->default(0);
            $table->integer('purchase_amount');
            $table->string('payment_mode')->nullable();
            $table->integer('points_awarded')->nullable();
            $table->integer('points_used')->nullable();
            $table->string('receipt_number')->nullable();
            $table->foreignId('subscription_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('merchant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('points');
    }
};
