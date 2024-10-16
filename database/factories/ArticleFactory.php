<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        // Create sample sources
        $source = Source::factory()->create();
        
        return [
            'author' => $this->faker->name,
            'keyword' => $this->faker->word,
            'category' => $this->faker->word,
            'content' => $this->faker->text,
            'published_date' => $this->faker->date,
            'source_id' => $source->id,
        ];
    }
}
