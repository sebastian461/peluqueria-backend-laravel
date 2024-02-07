<?php

namespace App\Http\Resources\userEvent;

use App\Models\Event;
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
        $event = Event::find($request->id);

        return [
            "message" => "Created",
            "user" => [
                "id" => $request->user()->id,
                "name" => $request->user()->name,
                "email" => $request->user()->email,
            ],
            "event" => [
                "name" => $event->name,
                "amoun" => $event->amount
            ]
        ];
    }
}
