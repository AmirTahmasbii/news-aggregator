<?php

namespace Tests\Feature\Controllers\Article;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\Preference;
use App\Models\Source;
use App\Models\User;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_articles_without_search()
    {
        // Create sample articles
        Article::factory()->count(5)->create();

        // Create a user and simulate authentication
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Send GET request to the fetch endpoint
        $response = $this->getJson('/api/article');

        // Check the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'total_items',
                    'total_pages',
                    'per_page',
                    'current_page',
                    'articles' => [
                        '*' => ['id', 'author', 'keyword', 'category', 'content', 'published_date', 'source']
                    ]
                ]
            ]);
    }

    public function test_fetch_articles_with_search()
    {
        // Create sample sources
        $source = Source::factory()->create();

        // Create sample articles with known data
        Article::factory()->create([
            'keyword' => 'tech-news',
            'content' => 'blah blah',
            'published_date' => now(),
            'category' => 'technology',
            'source_id' => $source->id
        ]);

        // Create a user and simulate authentication
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Send GET request to the fetch endpoint with search query
        $response = $this->getJson('/api/article?search=tech-news');

        // Check that the returned articles match the search criteria
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'articles' => [
                        '*' => ['id', 'author', 'keyword', 'category', 'content', 'published_date', 'source']
                    ]
                ]
            ]);
    }

    public function test_retrieve_single_article()
    {
        // Create sample sources
        $source = Source::factory()->create();

        // Create a sample article
        $article = Article::factory()->create([
            'keyword' => 'tech-news',
            'content' => 'blah blah',
            'published_date' => now(),
            'category' => 'technology',
            'source_id' => $source->id
        ]);

        // Create a user and simulate authentication
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Send GET request to retrieve the specific article
        $response = $this->getJson("/api/article/{$article->id}");

        // Check that the response includes the correct article
        $response->assertStatus(200)
            ->assertJsonStructure([
                "status",
                "data" => [
                    "articles" => ["id", "author", "keyword", "category", "content", "published_date", "source", "created_at", "updated_at"]
                ]
            ]);
    }

    public function test_feed_articles_with_preferences()
    {
        // Simulate a user and set preferences
        $user = User::factory()->create();
        $this->actingAs($user);

        Preference::factory()->create([
            'user_id' => $user->id,
            'categories' => ['technology']
        ]);

        // Create sample sources
        $source = Source::factory()->create();

        // Create an article that matches the user's preferences
        Article::factory()->create([
            'keyword' => 'tech-news',
            'content' => 'blah blah',
            'published_date' => now(),
            'category' => 'technology',
            'source_id' => $source->id
        ]);

        // Send GET request to the feed endpoint
        $response = $this->getJson('/api/article/feed');

        // Assert the response contains the article
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'articles' => [
                        '*' => ['id', 'author', 'keyword', 'category', 'content', 'published_date', 'source']
                    ]
                ]
            ]);
    }

    public function test_feed_articles_no_preferences()
    {
        // Simulate a user without preferences
        $user = User::factory()->create();
        $this->actingAs($user);

        // Send GET request to the feed endpoint
        $response = $this->getJson('/api/articles/feed');

        // Assert the error 404
        $response->assertStatus(404);
    }
}
