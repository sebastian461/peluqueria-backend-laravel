<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      "message" => "User login",
      "user" => [
        "id" => $this->id,
        "name" => $this->name,
        "email" => $this->email,
        "token" => [
          "token_type" => "Bearer",
          "token" => $this->createToken('auth_token', ["*"], now()->addHour(2))->plainTextToken
        ]
      ]
    ];
  }
}
