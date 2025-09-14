<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedule_students', function (Blueprint $table) {
            $table->id();
            $table->json('schedule_ids');
            $table->unsignedBigInteger('student_id')->unique();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_students');
    }
};
