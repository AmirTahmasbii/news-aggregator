<?php

namespace App\Services\Sources\Guardian;

use App\Models\Article;
use App\Models\Source;
use Carbon\Carbon;
use DateTimeImmutable;
use Guardian\GuardianAPI;
use Illuminate\Support\Facades\Log;

class client
{
    public $api, $source;

    public function __construct()
    {
        $this->source = Source::where('name', 'Guardian')->first();
        $this->api = new GuardianAPI($this->source->api_key);
    }

    public function getCategories()
    {
        $response =  ($this->api->sections()->fetch());

        $categories = [];

        foreach ($response->response->results as $item) {
            $categories[] = $item->id;
        }

        return $categories;
    }

    public function fetch(
        $q = '',
        $category = '',
        $from = null,
        $to = null,
        $page_size = 199,
        $page = 1
    ) {
        $request = $this->api
            ->content()
            ->setQuery($q);

            if(!empty($from)){
                $request->setFromDate($from);
            }

            if(!empty($to)){
                $request->setToDate($to);
            }

        $response = $request
            ->setShowTags('keyword')
            ->setShowReferences('author')
            ->setSection($category)
            ->setPageSize($page_size)
            ->setPage($page)
            ->fetch();

        return $response->response->results;
    }

    public function update()
    {
        try {
            $database = [];
            $output[] =  $this->fetch('', '', (new DateTimeImmutable())->modify('-1 day'), (new DateTimeImmutable()));

            foreach ($output[0] as $articles) {

                $database[] = [
                    'author' => $articles->references->author ?? '',
                    'keyword' => $articles->sectionId,
                    'category' => $articles->sectionId,
                    'content' => $articles->webUrl,
                    'published_date' => Carbon::parse($articles->webPublicationDate)->format('Y-m-d H:i:s'),
                    'source_id' => $this->source->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Article::insert($database);

            Log::info('guardian updated');

            return ['status' => 'success', 'message' => 'updated success!'];
        } catch (\Throwable $th) {
            dd($th);
            // return $this->parseError($th);
        }
    }
}
