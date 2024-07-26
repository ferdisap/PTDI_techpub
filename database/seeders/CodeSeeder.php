<?php

namespace Database\Seeders;

use App\Models\Code;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CodeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Schema::dropIfExists('code');
    Schema::create('code', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->string('type')->nullable();
      $table->text('description')->nullable();
    });

    // CSDB history code
    Code::create([
      'name' => 'CSDB-DELL',
      'description' => 'Delete CSDB Object',
      'type' => 'CSDB-code',
    ]);
    Code::create([
      'name' => 'CSDB-PDEL',
      'description' => 'Permanent delete CSDB Object', // there is no storage disk and cannot restore
      'type' => 'CSDB-code',
    ]);
    Code::create([
      'name' => 'CSDB-CRBT',
      'description' => 'Create CSDB Object', // there is no storage disk and cannot restore
      'type' => 'CSDB-code',
    ]);
    Code::create([
      'name' => 'CSDB-UPDT',
      'description' => 'Update content CSDB Object', // there is no storage disk and cannot restore
      'type' => 'CSDB-code',
    ]);
    Code::create([
      'name' => 'CSDB-PATH',
      'description' => 'Update path CSDB Object database', // there is no storage disk and cannot restore
      'type' => 'CSDB-code',
    ]);
    Code::create([
      'name' => 'CSDB-STRG',
      'description' => 'Update storage CSDB Object database', // there is no storage disk and cannot restore
      'type' => 'CSDB-code',
    ]);
    Code::create([
      'name' => 'CSDB-RSTR',
      'description' => 'Restore CSDB Object database',
      'type' => 'CSDB-code',
    ]);
    Code::create([
      'name' => 'CSDB-DDNC',
      'description' => 'Create DDN Object database',
      'type' => 'CSDB-code',
    ]);
    Code::create([
      'name' => 'CSDB-IMPT',
      'description' => 'CSDB Object has imported to storage',
      'type' => 'CSDB-code',
    ]);

    // User history code
    Code::create([
      'name' => 'USER-CRBT',
      'description' => 'User create the CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);
    Code::create([
      'name' => 'USER-UPDT',
      'description' => 'User update the CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);
    Code::create([
      'name' => 'USER-DELL',
      'description' => 'User delete the CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);
    Code::create([
      'name' => 'USER-PDEL',
      'description' => 'User permanent delete the CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);
    Code::create([
      'name' => 'USER-PATH',
      'description' => 'User update path the CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);
    Code::create([
      'name' => 'USER-STRG',
      'description' => 'User update storage the CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);
    Code::create([
      'name' => 'USER-RSTR',
      'description' => 'User restore the CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);
    Code::create([
      'name' => 'USER-DDNC',
      'description' => 'User create DDN CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);
    Code::create([
      'name' => 'USER-IMPT',
      'description' => 'User import CSDB Object', // there is no storage disk and cannot restore
      'type' => 'USER-code',
    ]);

    
    // PTDI CAGE Code
    Code::create([
      'name' => '0001Z',
      'description' => 'cade code for PTDI',
      'type' => 'CAGE-code',
    ]);

    // fake code
    for ($i=0; $i < 10; $i++) { 
      Code::create([
        'name' => Str::random(5),
        'description' => fake()->text(100),
        'type' => fake()->text(10),
      ]);
    }
  }
}
