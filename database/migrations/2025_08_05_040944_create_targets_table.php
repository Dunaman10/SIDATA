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
    if (!Schema::hasTable('targets')) {
      Schema::create('targets', function (Blueprint $table) {
        $table->id();
        $table->integer('count_target');
        $table->foreignId('id_class')->constrained('classes');
        $table->integer('date_target');
        $table->timestamps();
      });
    }
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('targets');
  }
};
