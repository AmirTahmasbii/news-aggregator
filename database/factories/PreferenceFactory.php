<?php

namespace Database\Factories;

use App\Models\Preference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreferenceFactory extends Factory
{
    protected $model = Preference::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'authors' => json_encode([$this->faker->name]),
            'categories' => json_encode([$this->faker->word]),
        ];
    }
}
