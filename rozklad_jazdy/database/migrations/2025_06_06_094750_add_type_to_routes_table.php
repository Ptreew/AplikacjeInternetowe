<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Route;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->enum('type', ['city', 'intercity'])->after('line_id')->default('city');
        });

        // Update existing records based on line_id
        // If line_id is not null, it's a city route, otherwise intercity
        DB::table('routes')->whereNull('line_id')->update(['type' => 'intercity']);
        DB::table('routes')->whereNotNull('line_id')->update(['type' => 'city']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
