<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoginResource;
use App\Http\Resources\RegisterResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $request->validate(([
      "name" => "required",
      "email" => "required|email|unique:users",
      "password" => "required"
    ]));

    $user = User::create([
      "name" => $request->name,
      "email" => $request->email,
      "password" => Hash::make($request->password)
    ]);

    return new RegisterResource($user);
  }

  public function login(Request $request)
  {
    $request->validate(([
      "email" => "required|email",
      "password" => "required"
    ]));

    if (!Auth::attempt($request->only("email", "password"))) {
      return response()->json([
        "message" => "Unathorized"
      ], 401);
    }

    $user = User::where("email", $request["email"])->firstOrFail();

    return new LoginResource($user);
  }

  public function logout(Request $request)
  {
    $request->user()->tokens()->delete();

    return [
      'message' => 'You have successfully logged out and the token was successfully deleted'
    ];
  }
}
