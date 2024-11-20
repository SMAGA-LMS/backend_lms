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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('file')->nullable();
            // CHANGE: terlalu tighly coupled, yang mana buat modules ini cuma punya si course
            // ketika ada module yang pingin jadi specific ke sebuah class_enrollment, maka mesti buat table baru
            // dengan column yang sama persis kayak table modules, beda nya course_id diganti dengan class_enrollment_id
            // Alasan lain kalau course_id tetap ada, kemudian class_enrollment_id juga ada,
            // maka kemungkinan ketika module mau dijadiin starter, class_enrollment_id nya mesti null
            // begitu sebaliknya ketika module mau dispesifik ke class_enrollment, course_id nya mesti null
            // $table->unsignedBigInteger('course_id')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
