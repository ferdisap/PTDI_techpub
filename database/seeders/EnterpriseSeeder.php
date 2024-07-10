<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnterpriseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // \App\Models\User::factory(10)->create();

    Schema::connection('sqlite')->dropIfExists('enterprises');

    Schema::connection('sqlite')->create('enterprises', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('code');
      $table->json('address');
      $table->json('remarks')->nullable();
    });

    DB::connection('sqlite')->table('enterprises')->insert([
      'name' => 'PT Dirgantara Indonesia',
      'code' => '0001Z',
      'address' => json_encode([
        "city" => 'Bandung',
        "country" => "Indonesia",
        'department' => '',
        'street' => '',
        'postOfficeBox' => '',
        'postalZipCode' => '',
        'state' => '',
        'province' => '',
        'building' => '',
        'room' => '',
        'phoneNumber' => [],
        'faxNumber' => [],
        'email' => [],
        'internet' => [],
        'SITA' => '',
      ]),
    ]);

    \App\Models\Enterprise::factory()->count(3)->create();
  }
}
