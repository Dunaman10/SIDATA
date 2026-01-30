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
    Schema::create('profiles', function (Blueprint $table) {
      $table->id();
      $table->string('title', 255)->nullable();
      $table->string('subtitle', '255')->nullable();
      $table->string('banner_image', 255)->nullable();
      $table->text('about')->nullable();
      $table->string('vision', 255)->nullable();
      $table->string('mission', 255)->nullable();
      $table->bigInteger('phone')->nullable();
      $table->string('email', 255)->nullable();
      $table->string('address', 255)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('profiles');
  }
};
