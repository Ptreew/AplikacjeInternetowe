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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->json('days_of_week')->comment('JSON array of days: [0,1,2,3,4,5,6] where 0-Sunday, 1-Monday, etc.');
            $table->timestamps();
            
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
