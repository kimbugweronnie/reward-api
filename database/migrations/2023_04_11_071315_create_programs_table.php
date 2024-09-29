<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('product');
            $table->string('percentage');
            $table->datetime('start_date');
            $table->datetime('due_date');
            $table->boolean('status')->default(1);
            $table->boolean('expired')->default(0);
            $table->foreignId('merchant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('programs');
    }
};
