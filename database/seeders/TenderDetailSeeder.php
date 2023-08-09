<?php

namespace Database\Seeders;

use App\Models\TenderDetail;
use Illuminate\Database\Seeder;

class TenderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TenderDetail::factory()->count(5)->create();
    }
}
