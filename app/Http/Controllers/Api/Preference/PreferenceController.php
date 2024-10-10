<?php

namespace App\Http\Controllers\Api\Preference;

use App\Http\Controllers\Controller;
use App\Http\Requests\Preference\SetRequest;
use App\Http\Resources\Preference\PreferenceResource;
use App\Models\Preference;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    public function retrieve()
    {
        return response()->json(['status' => 'success', 'data' => new PreferenceResource(Preference::where('user_id', auth()->id())->first())]);
    }

    public function set(SetRequest $request)
    {
        $validatedData = $request->validated();

        $preference = Preference::create([
            'source' => $validatedData['source'] ?? '',
            'category' => $validatedData['category'] ?? '',
            'author' => $validatedData['author'] ?? '',
            'user_id' => auth()->id(),
        ]);

        return response()->json(['status' => 'success', 'data' => new PreferenceResource($preference)]);
    }
}
