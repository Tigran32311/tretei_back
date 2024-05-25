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
//        Schema::create('app_status', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//            $table->string('name');
//            $table->string('xml_id');
//        });
//
//        Schema::create('appointment', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//            $table->string('appointment_file');
//        });

        Schema::table('appointment', function (Blueprint $table) {
//            $table->unsignedBigInteger('user_id')->unsigned();
//            $table->foreign('user_id')->references('id')->on('simple_users');
//            $table->unsignedBigInteger('plaintiff_id')->unsigned();
//            $table->foreign('plaintiff_id')->references('id')->on('simple_users');
//            $table->unsignedBigInteger('defendant_id')->unsigned();
//            $table->foreign('defendant_id')->references('id')->on('simple_users');
            $table->unsignedBigInteger('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('app_status');
        });

//        Schema::create('third_parties', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//            $table->unsignedBigInteger('user_id')->unsigned();
//            $table->foreign('user_id')->references('id')->on('simple_users');
//            $table->unsignedBigInteger('app_id')->unsigned();
//            $table->foreign('app_id')->references('id')->on('appointment');
//        });
//
//        Schema::create('docs_app', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//            $table->string('name');
//            $table->string('file_link');
//            $table->unsignedBigInteger('app_id')->unsigned();
//            $table->foreign('app_id')->references('id')->on('appointment');
//        });
//
//        Schema::create('chancellery', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//        });
//        Schema::table('chancellery',function (Blueprint $table) {
//            $table->unsignedBigInteger('user_id')->unsigned();
//            $table->foreign('user_id')->references('id')->on('users');
//        });
//
//        Schema::create('comments_appointment', function (Blueprint $table) {
//            $table->id();
//            $table->timestamps();
//            $table->text('comment');
//        });
//        Schema::table('comments_appointment', function (Blueprint $table) {
//            $table->unsignedBigInteger('ch_user_id')->unsigned();
//            $table->foreign('ch_user_id')->references('id')->on('chancellery');
//        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
