<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\events\CreateEventResource;
use App\Http\Resources\events\GetEventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $events = Event::all();
    return new GetEventResource($events);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate(([
      "name" => "required|unique:events",
      "amount" => "required|numeric"
    ]));

    $event = Event::create([
      "name" => $request->name,
      "amount" => $request->amount
    ]);

    return new CreateEventResource($event);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $request->validate(([
      "amount" => "required|numeric"
    ]));

    $event = Event::find($id);
    $event->amount = $request->amount;
    $event->save();

    return new CreateEventResource($event);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    $event = Event::find($id);
    $event->delete();

    $data = [
      "message" => "Event deleted",
      "event" => $event
    ];

    return response()->json($data);
  }
}
