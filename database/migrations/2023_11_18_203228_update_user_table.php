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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->default('');
            $table->string('last_name')->default('');
            $table->string('second_name')->default('');
            $table->date('birthday')->default('');
            $table->boolean('is_active')->default('');
            $table->unsignedBigInteger('role')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
