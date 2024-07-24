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
        // Schema::create('users', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('password');
        //     $table->string('role');
        //     $table->string('avatar')->nullable();
        //     $table->timestamps();
        // });

        DB::statement(
            'CREATE TABLE users (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                role_id BIGINT UNSIGNED NOT NULL,
                status_id BIGINT UNSIGNED NOT NULL,

                username VARCHAR(16) NOT NULL UNIQUE,
                password VARCHAR(64) NOT NULL,
                full_name VARCHAR(64) NOT NULL,
                email VARCHAR(64) NULL UNIQUE,
                avatar VARCHAR(64) NULL,
                gender ENUM("male", "female"),
                birth_date DATE,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NULL,

                FOREIGN KEY (role_id) REFERENCES roles(id),
                FOREIGN KEY (status_id) REFERENCES statuses(id)
            )'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
