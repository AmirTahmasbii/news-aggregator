<?php

namespace App\Services\Sources\NYT;

use App\Models\Source;

class seeder
{
    public static function run()
    {
        $source = Source::create([
            'name' => 'NYT',
            'api_key' => config('auth.api_key.nyt'),
        ]);

        $categories = (new client)->getCategories();
        $source->categories = $categories;
        $source->save();
    }
}
