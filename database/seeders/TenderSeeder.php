<?php

namespace Database\Seeders;

use App\Models\Tender;
use Illuminate\Database\Seeder;

class TenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tender::factory()->count(5)->create();
    }
}
