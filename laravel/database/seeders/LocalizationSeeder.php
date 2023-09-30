<?php

namespace Database\Seeders;

use App\Models\Localization;
use Illuminate\Database\Seeder;

class LocalizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Localization::firstOrCreate(['language_name' => 'English', 'locale' => 'en']);
        Localization::firstOrCreate(['language_name' => 'Deutsch', 'locale' => 'de']);
    }
}
