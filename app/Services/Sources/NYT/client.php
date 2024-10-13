<?php

namespace App\Services\Sources\NYT;

use App\Models\Source;
use jcobhams\NewsApi\NewsApi;

class client
{

    public $api;

    public function __construct()
    {
        $source = Source::where('name', 'NYT')->first();

        $this->api = new NewsApi($source->api_key);
    }

    public function getCategories()
    {
        return include __DIR__ . '/categories.php';
    }
}
