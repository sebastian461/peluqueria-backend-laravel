<?php

namespace App\Http\Resources\userEvent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreateUserEventResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {

    return [
      "message" => "Created",
      "event" => [
        "id" => $this->id,
        "title" => $this->title,
        "amount" => $this->amount,
        "start" => $this->start,
        "end" => $this->end,
        "user" => [
          "id" => $this->user_id,
          "name" => $this->user_name,
        ]
      ]
    ];
  }
}
