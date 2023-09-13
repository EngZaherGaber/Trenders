<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create(['email' => 'test@test.com']);
        Company::factory()->create(['user_id' => $user->id]);
        Company::factory()->count(5)->create();
    }
}
