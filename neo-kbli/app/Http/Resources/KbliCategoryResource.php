<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KbliCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code'           => $this->code,
            'name'           => $this->name,
            'description'    => $this->description,
            'sections_count' => $this->sections_count ?? 0,
        ];
    }
}
