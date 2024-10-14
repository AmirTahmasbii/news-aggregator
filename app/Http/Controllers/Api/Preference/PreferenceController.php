<?php

namespace App\Http\Controllers\Api\Preference;

use App\Http\Controllers\Controller;
use App\Http\Requests\Preference\SetRequest;
use App\Http\Resources\Preference\PreferenceResource;
use App\Models\Preference;
use App\Models\Source;

class PreferenceController extends Controller
{
    public function retrieve()
    {
        $preference = Preference::where('user_id', auth()->id())->first();

        if ($preference) {
            return response()->json(['status' => 'success', 'data' => new PreferenceResource($preference)]);
        }

        return response()->json(['status' => 'error', 'message' => 'this user has not set preference!'], 404);
    }

    public function set(SetRequest $request)
    {
        $validatedData = $request->validated();

        $preference = Preference::updateOrCreate(
            [
                'user_id' => auth()->id(),
            ],
            [
                'source_id' => Source::find($validatedData['source'])->id ?? '',
                'categories' => $validatedData['categories'] ?? '',
                'authors' => $validatedData['authors'] ?? '',
                'user_id' => auth()->id(),
            ]
        );

        return response()->json(['status' => 'success', 'data' => new PreferenceResource($preference)]);
    }
}
