<?php

namespace App\Http\Resources\userEvent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserEventCollection extends ResourceCollection
{
  /**
   * Transform the resource collection into an array.
   *
   * @return array<int|string, mixed>
   */
  public function toArray(Request $request): array
  {
    $events = [];

    foreach ($this->collection as $event) {
      array_push($events, [
        "id" => $event->id,
        "title" => $event->title,
        "amount" => $event->amount,
        "start" => $event->start,
        "end" => $event->end,
        "user" => [
          "id" => $event->user_id,
          "name" => $event->user_name,
        ]
      ]);
    }

    return $events;
  }
}
