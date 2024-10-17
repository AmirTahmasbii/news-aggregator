<?php
namespace Tests\Unit\Services\Sources\Guardian;

use Tests\TestCase;
use App\Services\Sources\Guardian\Client;
use App\Models\Source;
use Guardian\GuardianAPI;
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

        $this->mockAPI = Mockery::mock(GuardianAPI::class);
        $this->source = Source::factory()->create(['name' => 'Guardian', 'api_key' => 'test_api_key']);
        $this->client = new Client();
        $this->client->api = $this->mockAPI;
    }

    public function testGetCategories()
    {
        $mockResponse = (object)[
            'response' => (object)[
                'results' => [
                    (object)['id' => 'category1'],
                    (object)['id' => 'category2']
                ]
            ]
        ];

        $this->mockAPI->shouldReceive('sections->fetch')->andReturn($mockResponse);

        $categories = $this->client->getCategories();

        $this->assertEquals(['category1', 'category2'], $categories);
    }

    public function testFetch()
    {
        $mockResponse = (object)[
            'response' => (object)[
                'results' => [
                    (object)['webUrl' => 'http://example.com/article1', 'sectionId' => 'news'],
                    (object)['webUrl' => 'http://example.com/article2', 'sectionId' => 'sports']
                ]
            ]
        ];

        $this->mockAPI->shouldReceive('content->setQuery->setShowTags->setShowReferences->setSection->setPageSize->setPage->fetch')
                      ->andReturn($mockResponse);

        $articles = $this->client->fetch('test', 'news');

        $this->assertCount(2, $articles);
        $this->assertEquals('http://example.com/article1', $articles[0]->webUrl);
    }
}
