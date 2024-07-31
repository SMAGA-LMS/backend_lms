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
        // Schema::create('student_enrollments', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('user_id');
        //     $table->unsignedBigInteger('classroom_id');
        //     $table->timestamps();

        //     $table->foreign('user_id')->references('id')->on('users');
        //     $table->foreign('classroom_id')->references('id')->on('classrooms');
        // });

        DB::statement(
            'CREATE TABLE student_enrollments (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,

                class_period_id BIGINT UNSIGNED NOT NUll,
                student_id BIGINT UNSIGNED NOT NULL,

                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL,

                FOREIGN KEY (class_period_id) REFERENCES class_periods(id),
                FOREIGN KEY (student_id) REFERENCES students(user_id)
            )'
        );

        /*
            buat check agar tidak ada student terdaftar di dua kelas berbeda pada tahun ajaran yang sama
        */
        DB::unprepared(
            'CREATE TRIGGER check_academic_term_id_before_insert
            BEFORE INSERT ON student_enrollments
            FOR EACH ROW
            BEGIN
                DECLARE existing_academic_term_id BIGINT UNSIGNED;
                -- search academic_term_id dari class_period_id yang akan dimasukkan
                SELECT academic_term_id INTO existing_academic_term_id
                FROM class_periods
                WHERE id = NEW.class_period_id;

                -- check apakah student_id sudah terdaftar dalam academic_term_id yang sama
                IF EXISTS (
                    SELECT 1
                    FROM student_enrollments AS se
                    JOIN class_periods AS cp ON se.class_period_id = cp.id
                    WHERE se.student_id = NEW.student_id
                        AND cp.academic_term_id = existing_academic_term_id
                ) THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Student is already enrolled in a class period for this academic term";
                END IF;
            END
            '
        );

        DB::unprepared(
            'CREATE TRIGGER check_academic_term_id_before_update
            BEFORE UPDATE ON student_enrollments
            FOR EACH ROW
            BEGIN
                DECLARE existing_academic_term_id BIGINT UNSIGNED;

                -- search academic_term_id dari class_period_id yang akan dimasukkan
                SELECT academic_term_id INTO existing_academic_term_id
                FROM class_periods
                WHERE id = NEW.class_period_id;

                -- check apakah student_id sudah terdaftar dalam academic_term_id yang sama
                IF EXISTS (
                    SELECT 1
                    FROM student_enrollments AS se
                    JOIN class_periods AS cp ON se.class_period_id = cp.id
                    WHERE se.student_id = NEW.student_id
                        AND cp.academic_term_id = existing_academic_term_id
                        AND se.id != NEW.id -- This condition avoids checking the same row being updated
                ) THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Student is already enrolled in a class period for this academic term";
                END IF;
            END;
            '
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
        DB::unprepared('DROP TRIGGER IF EXISTS check_academic_term_id_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS check_academic_term_id_before_update');
    }
};
