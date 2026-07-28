<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'tracking_code'       => $this->tracking_code,
            'product_name'        => $this->product_name,
            'product_material'    => $this->product_material,
            'production_process'  => $this->production_process,
            'product_description' => $this->product_description,
            'saker_address'       => $this->saker_address,
            'status'              => is_object($this->status) ? $this->status->value : $this->status,
            'admin_notes'         => $this->admin_notes,
            'submitted_at'        => $this->submitted_at?->toIso8601String(),
            'reviewed_at'         => $this->reviewed_at?->toIso8601String(),
            'kbli_item'           => new KbliItemResource($this->whenLoaded('kbliItem')),
            'histories'           => ApplicationHistoryResource::collection($this->whenLoaded('histories')),
            'media'               => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
