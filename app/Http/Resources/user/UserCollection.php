<?php

namespace App\Http\Resources\user;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
  /**
   * Transform the resource collection into an array.
   *
   * @return array<int|string, mixed>
   */
  public function toArray(Request $request): array
  {

    $users = [];

    foreach ($this->collection as $user) {
      array_push($users, [
        "id" => $user->id,
        "name" => $user->name
      ]);
    }


    return [
      "message" => "Users",
      "users" => $users
    ];
  }
}
