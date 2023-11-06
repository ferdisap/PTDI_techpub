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
    // Schema::connection('techpub_sqlite')->dropIfExists('csdb');
    Schema::connection('sqlite')->create('csdb', function (Blueprint $table) {
      $table->id();
      $table->string('path');
      $table->string('status'); // new/changed/deleted
      $table->text('description')->nullable();
      $table->integer('initiator_id');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Schema::connection('techpub_sqlite')->dropIfExists('csdb');
    Schema::connection('sqlite')->drop('csdb');
  }
};
