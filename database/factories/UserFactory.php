<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      // 'name' => fake()->name(),
      // 'email' => fake()->unique()->safeEmail(),
      // 'email_verified_at' => now(),
      // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
      // 'remember_token' => Str::random(10),
      'first_name' => fake()->firstName(),
      'middle_name' => fake()->lastName(),
      'last_name' => fake()->lastName(),
      'job_title' => fake()->jobTitle(),
      'email' => fake()->unique()->safeEmail(),
      'password' => '$2y$10$BkzZhuRUrW2UWnGzQmGWLOIMj4P17o9lRH1HoSx7qHubAyYH8T/7q', // 'password'
      'address' => json_encode([
        "city" => fake()->city(),
        "country" => fake()->country()
      ]),
      'remember_token' => Str::random(10)
    ];
  }

  /**
   * Indicate that the model's email address should be unverified.
   */
  public function unverified(): static
  {
    return $this->state(fn (array $attributes) => [
      'email_verified_at' => null,
    ]);
  }
}
