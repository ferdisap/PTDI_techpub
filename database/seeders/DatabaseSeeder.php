<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // \App\Models\User::factory(10)->create();

    \App\Models\User::factory()->create([
        'name' => 'Luffy',
        'email' => 'luffy@example.com',
        'password' => '$2y$10$BkzZhuRUrW2UWnGzQmGWLOIMj4P17o9lRH1HoSx7qHubAyYH8T/7q', // 'password'
    ]);
  }
}
