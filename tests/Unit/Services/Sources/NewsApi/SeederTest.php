<?php

namespace Tests\Unit\Services\Sources\NewsApi;

use Tests\TestCase;
use App\Services\Sources\NewsApi\Seeder;
use App\Services\Sources\NewsApi\Client;
use App\Models\Source;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected $seeder;
    protected $mockClient;
    protected $source;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = Mockery::mock(Client::class);
        $this->source = Source::factory()->create(['name' => 'NewsApi', 'api_key' => 'test_api_key']);
        Seeder::$client = $this->mockClient;
        Seeder::$source = $this->source;
    }

    public function testRun()
    {
        $mockCategories = ['business', 'entertainment'];
        $mockArticles = (object)[
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

        $this->mockClient->shouldReceive('getCategories')->andReturn($mockCategories);
        $this->mockClient->shouldReceive('fetch')->andReturn($mockArticles);

        Seeder::run();

        $this->assertDatabaseHas('sources', ['name' => 'NewsApi']);
        $this->assertDatabaseHas('articles', ['content' => 'Content 1']);
        $this->assertDatabaseHas('articles', ['content' => 'Content 2']);
    }
}

