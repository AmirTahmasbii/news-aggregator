<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\Preference\SetRequest;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SetRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_passes_validation_with_valid_data()
    {
        // Create a source instance in the database
        $source = Source::factory()->create(['name' => 'NEWSAPI']);

        // Valid request data
        $data = [
            'source' => 'NEWSAPI',
            'categories' => 'technology,science',
            'authors' => 'John Doe,Jane Doe',
        ];

        // Perform validation
        $request = new SetRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation passes
        $this->assertTrue($validator->passes());
    }

    public function test_it_fails_validation_with_invalid_source()
    {
        // Invalid request data (source does not exist)
        $data = [
            'source' => 'INVALID_SOURCE',
            'categories' => 'technology,science',
            'authors' => 'John Doe,Jane Doe',
        ];

        // Perform validation
        $request = new SetRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('source', $validator->errors()->toArray());
    }

    public function test_it_fails_validation_when_categories_or_authors_are_not_strings()
    {
        // Invalid request data (categories and authors are not strings)
        $data = [
            'source' => 'NEWSAPI',
            'categories' => ['not', 'a', 'string'], // Array instead of string
            'authors' => 12345, // Integer instead of string
        ];

        // Perform validation
        $request = new SetRequest();
        $validator = Validator::make($data, $request->rules());

        // Assert that validation fails
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('categories', $validator->errors()->toArray());
        $this->assertArrayHasKey('authors', $validator->errors()->toArray());
    }
}
