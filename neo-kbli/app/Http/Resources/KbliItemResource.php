<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KbliItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parent = null;
        if (isset($this->parent_data)) {
            $parent = $this->parent_data;
        } elseif ($this->relationLoaded('section') && $this->section && $this->section->category) {
            $parent = [
                'code' => $this->section->category->code,
                'name' => $this->section->category->name,
            ];
        }

        return [
            'code'        => $this->code,
            'name'        => $this->name,
            'description' => $this->description,
            'level'       => $this->level ?? (strlen($this->code) <= 2 ? 'category' : 'item'),
            'parent'      => $parent,
            'children'    => $this->children_data ?? [],
        ];
    }
}
