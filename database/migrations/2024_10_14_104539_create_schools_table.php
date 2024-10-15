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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->integer('num_of_programs')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('region_id')->nullable();
            $table->char('category')->nullable();
            $table->string('is_special_boarding_catchment_area')->default('NO');
            $table->string('is_cluster')->default('NO');
            $table->string('track')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
