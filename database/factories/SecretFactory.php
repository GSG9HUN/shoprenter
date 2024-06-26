<?php

namespace Database\Factories;

use App\Models\Secret;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Secret>
 */
class SecretFactory extends Factory
{
    protected $model = Secret::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create();
        $secretText = $faker->sentence;
        $afterExpire = $this->faker->numberBetween(0, 10);
        return [
            'hash' => hash('sha256', $secretText),
            'secretText' => $secretText,
            'createdAt' => Carbon::now(),
            'expireAfter' => Carbon::now()->addMinutes($afterExpire),
            'expireAfterViews' => $this->faker->numberBetween(1, 10),
        ];
    }
}
