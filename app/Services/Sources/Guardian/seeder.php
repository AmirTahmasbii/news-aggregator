<?php

namespace App\Services\Sources\Guardian;

use App\Models\Source;

class seeder
{
    public static function run()
    {
        $source = Source::create([
            'name' => 'Guardian',
            'api_key' => config('auth.api_key.guardian'),
        ]);

        $categories = (new client)->getCategories();
        $source->categories = json_encode($categories);
        $source->save();
    }
}
