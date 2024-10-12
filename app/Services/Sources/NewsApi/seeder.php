<?php

namespace App\Services\Sources\NewsApi;

use App\Models\Source;

class seeder
{
    public static function run()
    {
        $source = Source::create([
            'name' => 'NewsApi',
            'api_key' => config('auth.api_key.news_api'),
        ]);

        $categories = (new client)->getCategories();
        $source->categories = json_encode($categories);
        $source->save();
    }
}
