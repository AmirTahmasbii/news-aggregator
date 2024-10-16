<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        return [
            'author' => $this->faker->name,
            'keyword' => $this->faker->word,
            'category' => $this->faker->word,
            'content' => $this->faker->text,
            'published_date' => $this->faker->date,
            'source_id' => 1,
        ];
    }
}
