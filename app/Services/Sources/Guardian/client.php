<?php

namespace App\Services\Sources\Guardian;

use App\Models\Source;
use Guardian\GuardianAPI;

class client
{
    public $api;

    public function __construct()
    {
        $api_key = Source::where('name', 'Guardian')->first();
        $this->api = new GuardianAPI($api_key->api_key);
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
        $page_size = 199,
        $page = 1
    ) {
        $response = $this->api
            ->content()
            ->setQuery($q)
            ->setShowTags('keyword')
            ->setShowReferences('author')
            ->setSection($category)
            ->setPageSize($page_size)
            ->setPage($page)
            ->fetch();

        return $response->response->results;
    }

    public function update() {}
}
