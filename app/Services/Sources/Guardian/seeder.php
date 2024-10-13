<?php

namespace App\Services\Sources\Guardian;

use App\Models\Article;
use App\Models\Source;
use Carbon\Carbon;

class seeder
{
    public static $client, $source;

    public static function run()
    {
        self::source();
        self::article();
    }

    private static function source()
    {
        $source = Source::create([
            'name' => 'Guardian',
            'api_key' => config('auth.api_key.guardian'),
        ]);

        if (empty(static::$client)) {
            static::$client = new client;
            static::$source = $source;
        }

        $categories = (static::$client)->getCategories();
        $source->categories = $categories;
        $source->save();
    }

    private static function article()
    {
        $api = static::$client;

        $categories = $api->getCategories();

        foreach ($categories as $item) {
            $output[] = $api->content()->setSection('technology')->fetch();
            foreach ($output[0]->articles as $articles) {

                $database[] = [
                    'author' => $articles->author ?? '',
                    'keywords' => json_encode($item),
                    'categories' => json_encode($item),
                    'content' => $articles->content,
                    'published_date' => Carbon::parse($articles->publishedAt)->format('Y-m-d H:i:s'),
                    'source_id' => (static::$source)->id,
                ];
            }
        }

        Article::insert($database);
    }
}
