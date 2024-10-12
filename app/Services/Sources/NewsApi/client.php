<?php
namespace App\Services\Sources\NewsApi;

use App\Models\Source;
use jcobhams\NewsApi\NewsApi;

class client{

    public $api;

    public function __construct() {
        $api_key = Source::where('name', 'NewsApi')->first();

        $this->api = new NewsApi($api_key);
    }

    public function getCategories()
    {
        return $this->api->getCategories();
    }
}