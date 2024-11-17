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
        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('classroom_id')->references('id')->on('classrooms');
            $table->foreign('user_id')->references('id')->on('users');
        });

        // how to php artisan migrate specific migration
        // 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
