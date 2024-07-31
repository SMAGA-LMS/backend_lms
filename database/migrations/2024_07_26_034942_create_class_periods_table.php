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
        // Schema::create('class_periods', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        DB::statement(
            'CREATE TABLE class_periods (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,

                grade_classroom_id BIGINT UNSIGNED NOT NULL,
                academic_term_id BIGINT UNSIGNED NOT NULL,
                teacher_id BIGINT UNSIGNED NULL,

                class_period_code VARCHAR(32) NOT NULL UNIQUE,
                name VARCHAR(32) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL,

                FOREIGN KEY (grade_classroom_id) REFERENCES grade_classrooms(id),
                FOREIGN KEY (academic_term_id) REFERENCES academic_terms(id),
                FOREIGN KEY (teacher_id) REFERENCES teachers(user_id),

                UNIQUE (grade_classroom_id, academic_term_id)
            )'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_periods');
    }
};
