<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::create('grade_classrooms', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        DB::statement(
            'CREATE TABLE grade_classrooms (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,

                grade_level_id BIGINT UNSIGNED NOT NULL,
                classroom_id BIGINT UNSIGNED NOT NULL,

                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL,

                FOREIGN KEY (grade_level_id) REFERENCES grade_levels(id),
                FOREIGN KEY (classroom_id) REFERENCES classrooms(id),

                UNIQUE(grade_level_id, classroom_id)
            )'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_classrooms');
    }
};
