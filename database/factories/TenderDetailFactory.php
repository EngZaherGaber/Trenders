<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Tender;
use App\Models\TenderDetail;

class TenderDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TenderDetail::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->word,
            'question' => $this->faker->word,
            'description' => $this->faker->text,
            'data' => $this->faker->word,
            'tender_id' => Tender::factory(),
        ];
    }
}
