<?php

namespace Tests\Unit\Services\Sources\Guardian;

use Tests\TestCase;
use App\Services\Sources\Guardian\Seeder;
use App\Models\Source;
use Mockery;
use App\Services\Sources\Guardian\Client;
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
        $this->source = Source::factory()->create(['name' => 'Guardian', 'api_key' => 'test_api_key']);
        Seeder::$client = $this->mockClient;
        Seeder::$source = $this->source;
    }

    public function testRun()
    {
        $mockCategories = ['category1', 'category2'];
        $mockArticles = [
            (object)[
                'webUrl' => 'http://example.com/article1',
                'references' => (object)['author' => 'Author 1'],
                'webPublicationDate' => now()->toDateTimeString()
            ],
            (object)[
                'webUrl' => 'http://example.com/article2',
                'references' => (object)['author' => 'Author 2'],
                'webPublicationDate' => now()->toDateTimeString()
            ]
        ];

        $this->mockClient->shouldReceive('getCategories')->andReturn($mockCategories);
        $this->mockClient->shouldReceive('fetch')->andReturn($mockArticles);

        Seeder::run();

        $this->assertDatabaseHas('sources', ['name' => 'Guardian']);
        $this->assertDatabaseHas('articles', ['content' => 'http://example.com/article1']);
    }
}
