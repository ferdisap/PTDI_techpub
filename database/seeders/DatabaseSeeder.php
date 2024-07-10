<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // \App\Models\User::factory(10)->create();

    Schema::connection('sqlite')->dropIfExists('users');

    Schema::connection('sqlite')->create('users', function (Blueprint $table) {
      $table->id();
      $table->string('first_name');
      $table->string('middle_name')->nullable();
      $table->string('last_name')->nullable();
      $table->string('job_title');
      $table->integer('work_enterprise_id')->nullable();
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->json('address');
      $table->rememberToken();
      $table->timestamps();
    });

    \App\Models\User::factory()->create([
      'first_name' => 'Luffy',
      'middle_name' => 'Baka',
      'last_name' => 'Sencho',
      'job_title' => 'Captain',
      'work_enterprise_id' => 2,
      'email' => 'luffy@example.com',
      'password' => '$2y$10$BkzZhuRUrW2UWnGzQmGWLOIMj4P17o9lRH1HoSx7qHubAyYH8T/7q', // 'password'
      'address' => json_encode([
        "city" => 'fusa',
        "country" => "greenland"
      ]),
      'remember_token' => Str::random(10)
    ]);

    \App\Models\User::factory()->count(3)->create();
  }
}
