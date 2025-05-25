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
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('stop_id');
            $table->integer('stop_number');
            $table->integer('distance_from_start')->comment('Distance in meters from the starting point');
            $table->integer('time_to_next')->comment('Time in minutes to the next stop');
            $table->timestamps();
            
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('stop_id')->references('id')->on('stops')->onDelete('cascade');
            
            // Each stop should appear only once in a specific position on a route
            $table->unique(['route_id', 'stop_number']);
            $table->unique(['route_id', 'stop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_stops');
    }
};
