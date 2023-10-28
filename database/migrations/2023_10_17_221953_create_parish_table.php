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
        Schema::create('parish', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('phone1')->unique();
            $table->string('phone2')->nullable();
            $table->string('country');
            $table->string('state');
            $table->string('city')->nullable();
            $table->string('address');
            $table->string('parishname');
            $table->string('reportingcode');
            //$table->foreign('reportingcode')->references('code')->on('national')->onUpdate('cascade')->onDelete('set null');
            $table->string('picode')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parish');
    }
};
