<?php

namespace App\Http\Resources\Preference;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source' => $this->source->name,
            'category' => $this->categories,
            'authors' => $this->authors,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
