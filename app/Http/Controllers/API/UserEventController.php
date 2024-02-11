<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\userEvent\CreateUserEventResource;
use App\Http\Resources\userEvent\GetUserEventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserEventController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $event = DB::table("event_user")
      ->join("users", "event_user.user_id", "=", "users.id")
      ->join("events", "event_user.event_id", "=", "events.id")
      ->select(
        "event_user.id",
        "users.id as user_id",
        "users.name as user_name",
        "events.title",
        "events.amount",
        "event_user.start",
        "event_user.end"
      )
      ->get();

    return new GetUserEventResource($event);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request, string $id)
  {
    $user = $request->user();
    $user->events()->attach($id, ["start" => $request->start, "end" => $request->end]);

    $event = DB::table("event_user")
      ->join("users", "event_user.user_id", "=", "users.id")
      ->join("events", "event_user.event_id", "=", "events.id")
      ->select(
        "event_user.id",
        "users.id as user_id",
        "users.name as user_name",
        "events.title",
        "events.amount",
        "event_user.start",
        "event_user.end"
      )
      ->orderBy("event_user.id", "desc")->first();

    return new CreateUserEventResource($event);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Request $request, string $id)
  {
    $user = $request->user();
    $deleted = DB::table('event_user')->where("id", "=", $id)->delete();
    $user->events()->detach($id);

    return response()->json([
      "message" => "Deleted",
    ]);
  }
}
