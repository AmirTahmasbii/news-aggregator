<?php

namespace Tests\Unit\Services\Sources\NewsApi;

use Tests\TestCase;
use App\Services\Sources\NewsApi\Client;
use App\Models\Source;
use jcobhams\NewsApi\NewsApi;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected $client;
    protected $mockAPI;
    protected $source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockAPI = Mockery::mock(NewsApi::class);
        $this->source = Source::factory()->create(['name' => 'NewsApi', 'api_key' => 'test_api_key']);
        $this->client = new Client();
        $this->client->api = $this->mockAPI;
    }

    public function testGetCategories()
    {
        $mockCategories = ['business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology'];

        $this->mockAPI->shouldReceive('getCategories')->andReturn($mockCategories);

        $categories = $this->client->getCategories();

        $this->assertEquals($mockCategories, $categories);
    }

    public function testFetch()
    {
        $mockResponse = (object)[
            'status' => 'ok',
            'articles' => [
                (object)[
                    'author' => 'Author 1',
                    'content' => 'Content 1',
                    'publishedAt' => now()->toDateTimeString()
                ],
                (object)[
                    'author' => 'Author 2',
                    'content' => 'Content 2',
                    'publishedAt' => now()->toDateTimeString()
                ]
            ]
        ];

        $this->mockAPI->shouldReceive('getEverything')
            ->with('test', '', '', '', null, null, 'en', 'publishedAt', null, null)
            ->andReturn($mockResponse);

        $articles = $this->client->fetch('test');

        $this->assertCount(2, $articles->articles);
        $this->assertEquals('Author 1', $articles->articles[0]->author);
    }
}
