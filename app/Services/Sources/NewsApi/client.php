<?php

namespace App\Services\Sources\NewsApi;

use App\Models\Source;
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
        $page_size = null,
        $page = null
    ) {
        try {
            return $this->api->getEverything($q, $page_size, $page);
        } catch (\Throwable $th) {
            dd($th->getMessage());
            return $this->parseError($th);
        }
    }
    
    private function parseError($error)
    {
        if (!str_ends_with(explode("\n", ($error->getMessage()))[1], '}')) {
            $message = json_decode((explode("\n", ($error->getMessage()))[1]) . '"}', true)['message'];
            return response()->json(['status' => 'error', 'message' => $message], $error->getCode());
        }
        return response()->json(['status' => 'error', 'message' => $error->getMessage()], $error->getCode());
    }
}
