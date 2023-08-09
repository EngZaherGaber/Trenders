<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Institution;
use App\Models\User;

class InstitutionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Institution::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'address' => $this->faker->word,
            'created_at' => now(),
            'user_id' => User::factory(),
        ];
    }
}
