<?php

namespace Tests\Feature\Controllers\Preference;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Preference;
use App\Models\Source;
use App\Models\User;

class PreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_retrieve_user_preference_successfully()
    {
        // Create a user and simulate authentication
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        
        // Create sample sources
        $source = Source::factory()->create();

        // Create a preference for the user
        Preference::factory()->create([
            'user_id' => $user->id,
            'categories' => 'sports',
            'authors' => 'amir',
            'source_id' => $source->id,
        ]);

        // Send GET request to the retrieve endpoint
        $response = $this->getJson('/api/preference');

        // Check the response status and structure
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'data' => [
                         'categories' => 'sports',
                         'authors' => 'amir',
                         'source' => $source->name,
                     ]
                 ]);
    }

    public function test_retrieve_user_preference_not_set()
    {
        // Create a user and simulate authentication
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        // Send GET request to the retrieve endpoint without setting preferences
        $response = $this->getJson('/api/preferences');

        // Check that the response returns 404 when no preferences are set
        $response->assertStatus(404);
    }
}

