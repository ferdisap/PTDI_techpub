<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Filesystem\Filesystem;
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
    // Schema::connection('sqlite')->create('csdb', function (Blueprint $table) {
    //   $table->ulid('id')->primary();
    //   $table->string('filename')->unique();
    //   $table->string('path');
    //   $table->string('status'); // new/modified/deleted + seeded/initiated/ + unused
    //   $table->text('description')->nullable();
    //   // $table->boolean('editable'); // yes(1) or no(0) // sepertinya ini sudah tidak dipakai. awailnya dipakai di CsdbController@postupdate, tapi nanti dihapus saja. Kayaknya diawal ini berfungsi saat object sudah di customer, tidak bisa diedit lagi, padahal sekarang untuk customer pakai database sql yang berbeda (ietm.sqlite);
    //   $table->integer('initiator_id');
    //   $table->integer('project_name');
    //   $table->json('remarks')->nullable();
    //   $table->timestamps();
    // });
    $file = new Filesystem;
    $file->cleanDirectory('storage/csdb');
    
    Schema::connection('sqlite')->create('csdb', function (Blueprint $table) {
      $table->ulid('id')->primary();
      $table->string('filename')->unique();
      $table->string('path');
      $table->boolean('editable'); // yes(1) or no(0). Ketika di commit, editable menjadi 0 yang artinya tidak bisa di edit lagi. Sehingga harus naik index
      $table->integer('initiator_id');
      $table->json('remarks')->nullable();
      $table->timestampsTz(7);
    });

  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Schema::connection('techpub_sqlite')->dropIfExists('csdb');
    Schema::connection('sqlite')->drop('csdb');
    $file = new Filesystem;
    $file->cleanDirectory('storage/csdb');
  }
};
