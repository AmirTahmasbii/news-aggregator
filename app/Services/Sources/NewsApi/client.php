<?php

namespace App\Services\Sources\NewsApi;

use App\Models\Article;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use jcobhams\NewsApi\NewsApi;

class client
{

    public $api, $source;

    public function __construct()
    {
        $this->source = Source::where('name', 'NewsApi')->first();

        $this->api = new NewsApi($this->source->api_key);
    }

    public function getCategories()
    {
        return $this->api->getCategories();
    }

    public function fetch(
        $q = null,
        $from = null,
        $to = null,
        $page_size = null,
        $page = null
    ) {
        try {
            return $this->api->getEverything($q, '', '', '', $from, $to, 'en', 'publishedAt', $page_size, $page);
        } catch (\Throwable $th) {
            return $this->parseError($th);
        }
    }

    public function update()
    {
        try {
            $categories = $this->source->categories;
            foreach ($categories as $category) {
                $output[] =  ($this->fetch($category, now()->addDays(-1), now(), 100, 1));
                
                if (iS_array($output[0]) && $output[0]['status'] == 'error')
                    return ['status' => 'error', 'message' => $output[0]['message']];

                    foreach ($output[0]->articles as $articles) {

                        $database[] = [
                            'author' => $articles->author,
                            'keyword' => $category,
                            'category' => $category,
                            'content' => $articles->content,
                            'published_date' => Carbon::parse($articles->publishedAt)->format('Y-m-d H:i:s'),
                            'source_id' => $this->source->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
            }

            Article::insert($database);

            Log::info('news_api updated');

            return ['status' => 'success', 'message' => 'articles updated success!'];
        } catch (\Throwable $th) {
            return $this->parseError($th);
        }
    }

    private function parseError($error)
    {
        // if (!str_ends_with(explode("\n", ($error->getMessage()))[1], '}')) {
        //     $message = json_decode((explode("\n", ($error->getMessage()))[1]) . '"}', true)['message'];
        //     return response()->json(['status' => 'error', 'message' => $message], $error->getCode());
        // }

        return ['status' => 'error', 'message' => $error->getMessage()];
    }
}
