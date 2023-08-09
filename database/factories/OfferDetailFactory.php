<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Offer;
use App\Models\OfferDetail;
use App\Models\TenderDetail;

class OfferDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OfferDetail::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'tender_detail_id' => TenderDetail::factory(),
            'answer' => $this->faker->word,
        ];
    }
}
