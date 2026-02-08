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
    Schema::create('teaching_journals', function (Blueprint $table) {
      $table->id();
      $table->foreignId('schedule_id')->nullable()->constrained()->nullOnDelete();
      $table->foreignId('teacher_id')->constrained();
      $table->foreignId('classes_id')->constrained();
      $table->foreignId('lesson_id')->constrained();
      $table->date('date');
      $table->time('start_time')->nullable();
      $table->time('end_time')->nullable();
      $table->text('topic')->nullable();
      $table->text('notes')->nullable();
      $table->string('photo')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('teaching_journals');
  }
};
