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
        Schema::create('session_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_enrollment_id');
            $table->string('title');
            $table->string('description');
            $table->timestamp('date_time');
            $table->timestamps();

            $table->foreign('class_enrollment_id')->references('id')->on('class_enrollments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_records');
    }
};
