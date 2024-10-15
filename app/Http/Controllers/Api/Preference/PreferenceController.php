<?php

namespace App\Http\Controllers\Api\Preference;

use App\Http\Controllers\Controller;
use App\Http\Requests\Preference\SetRequest;
use App\Http\Resources\Preference\PreferenceResource;
use App\Models\Preference;
use App\Models\Source;
/**
 * @group Preferences
 *
 * APIs for managing preferences
 */
class PreferenceController extends Controller
{
    /**
     * Retrieve
     *
     * User Preferences Retrieve
     *
     * @authenticated
     * 
     * @response 200 scenario="success" {"status": "success","data": {"source": "news_api","category": "sports","author": "amir","created_at": "2024-10-10T10:14:48.000000Z","updated_at": "2024-10-10T10:14:48.000000Z"}}
     * @response 404 scenario="error" {"status": "error","message": "this user has not set preference!"}
     * 
     */
    public function retrieve()
    {
        $preference = Preference::where('user_id', auth()->id())->first();

        if ($preference) {
            return response()->json(['status' => 'success', 'data' => new PreferenceResource($preference)]);
        }

        return response()->json(['status' => 'error', 'message' => 'this user has not set preference!'], 404);
    }

    /**
     * Set
     *
     * User Preferences Set
     *
     * @authenticated
     * @bodyParam source string required between NEWSAPI, GUARDIAN, NYT. Example: NEWSAPI
     * @response 200 scenario="success" {"status": "success","data": {"source": "news_api","category": "sports","author": "amir","created_at": "2024-10-10T10:14:48.000000Z","updated_at": "2024-10-10T10:14:48.000000Z"}}
     * 
     */
    public function set(SetRequest $request)
    {
        $validatedData = $request->validated();

        $preference = Preference::updateOrCreate(
            [
                'user_id' => auth()->id(),
            ],
            [
                'source_id' => Source::where('name', $validatedData['source'])->first()->id ?? '',
                'categories' => explode(',', $validatedData['categories']) ?? '',
                'authors' => $validatedData['authors'] ?? '',
                'user_id' => auth()->id(),
            ]
        );

        return response()->json(['status' => 'success', 'data' => new PreferenceResource($preference)]);
    }
}
