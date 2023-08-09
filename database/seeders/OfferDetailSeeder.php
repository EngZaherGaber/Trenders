<?php

namespace Database\Seeders;

use App\Models\OfferDetail;
use Illuminate\Database\Seeder;

class OfferDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OfferDetail::factory()->count(5)->create();
    }
}
