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
        // Schema::create('teachers', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        DB::statement(
            'CREATE TABLE teachers (
                user_id BIGINT UNSIGNED PRIMARY KEY,

                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL,

                FOREIGN KEY (user_id) REFERENCES users(id)
            )'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
