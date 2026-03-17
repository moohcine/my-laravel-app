<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained()->cascadeOnDelete();
            $table->date('issue_date');
            $table->unsignedSmallInteger('hours_completed');
            $table->text('projects');
            $table->text('soft_skills')->nullable();
            $table->string('signed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};
