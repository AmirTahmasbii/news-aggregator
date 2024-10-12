<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Sources\Guardian\seeder as GuardianSeeder;
use App\Services\Sources\NewsApi\seeder as NewsApiSeeder;
use App\Services\Sources\NYT\seeder as NYTSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        GuardianSeeder::run();
        
        NYTSeeder::run();
        
        NewsApiSeeder::run();

    }
}
