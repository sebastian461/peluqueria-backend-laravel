<?php

namespace App\Http\Resources\events;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreateEventResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      "message" => "Service created/updated",
      "service" => [
        "id" => $this->id,
        "title" => $this->title,
        "amount" => $this->amount
      ]
    ];
  }
}
