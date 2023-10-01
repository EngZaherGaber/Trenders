<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            'trender_name' => $this->tender->title,
            'for' => $this->tender->institution->user->name,
            'work_on_it' => $this->created_at,
            'work_ended_it' => $this->tender->ended_at,
            'company_name' => $this->company->user->name,
            'offer_details' => $this->whenLoaded('offerDetails'),
        ];
    }
}
