<?php

namespace Tests\Unit\Resources\Article;

use App\Http\Resources\Article\ArticleResource;
use App\Models\Article;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_transforms_article_resource()
    {
        // Create a source and an article instance
        $source = Source::factory()->create(['name' => 'Guardian', 'api_key' => 'ashkdadlhashdj', 'categories' => ['sports', 'technology']]);
        
        $article = Article::factory()->create([
            'author' => 'John Doe',
            'keyword' => 'us-sports',
            'category' => 'sports',
            'content' => 'Some content here...',
            'published_date' => '2024-10-16',
            'source_id' => $source->id,
        ]);

        // Create the resource instance
        $resource = new ArticleResource($article);

        // Assert the transformed array matches the expected structure
        $this->assertEquals([
            'id' => $article->id,
            'author' => 'John Doe',
            'keyword' => 'us-sports',
            'category' => 'sports',
            'content' => 'Some content here...',
            'published_date' => '2024-10-16',
            'source' => 'Guardian',
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
        ], $resource->toArray(request()));
    }
}
