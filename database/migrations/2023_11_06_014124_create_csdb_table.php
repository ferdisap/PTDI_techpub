<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   * seeded: file exist in local, but not in database
   * initiated: file is created by user upload project metadata
   */
  public function up(): void
  {
    // Schema::connection('techpub_sqlite')->dropIfExists('csdb');
    Schema::connection('sqlite')->create('csdb', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->string('filename')->unique();
      $table->string('path');
      $table->string('status'); // new/modified/deleted + seeded/initiated/ + unused
      $table->text('description')->nullable();
      $table->boolean('editable'); // yes(1) or no(0)
      $table->integer('initiator_id');
      $table->integer('project_name');
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
