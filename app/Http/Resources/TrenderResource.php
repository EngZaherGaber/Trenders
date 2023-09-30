<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrenderResource extends JsonResource
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
            'title' => $this->title,
            'image' => $this->image,
            'description' => $this->description,
            'image' => $this->image,
            'columns_number' => $this->columns_number,
            'created_at' => $this->created_at,
            'ended_at' => $this->ended_at,
            'offers_count' => $this->offers()->count(),
            'best_offer_from' => $this->best_offer_from?->company->user->name,
            'offers' => OfferResource::collection($this->whenLoaded('offers')),
            'trender_details' => $this->whenLoaded('tenderDetails'),
        ];
    }
}
