<?php

namespace Tests\Unit\Resources\Preference;

use App\Http\Resources\Preference\PreferenceResource;
use App\Models\Preference;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreferenceResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_transforms_preference_resource()
    {
        // Create a source instance
        $source = Source::factory()->create(['name' => 'Guardian', 'api_key' => 'ashkdadlhashdj', 'categories' => ['sports', 'technology']]);

        // Create a preference instance
        $preference = Preference::factory()->create([
            'source_id' => $source->id,
            'categories' => 'technology,science',
            'authors' => 'John Doe,Jane Doe',
        ]);

        // Create the resource instance
        $resource = new PreferenceResource($preference);

        // Assert the transformed array matches the expected structure
        $this->assertEquals([
            'source' => 'Guardian',
            'categories' => 'technology,science',
            'authors' => 'John Doe,Jane Doe',
            'created_at' => $preference->created_at,
            'updated_at' => $preference->updated_at,
        ], $resource->toArray(request()));
    }
}
