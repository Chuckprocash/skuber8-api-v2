<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\TripCreated;
use App\Events\TripAccepted;
use App\Events\TripStarted;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Models\Trip;

class TripController extends Controller
{
    
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required',
        ]);

        // Logic to create a new trip
        // dd($request->all());
        $newUserTrip = $request->user()->trips()->create($request->only([
            'origin',
            'destination',
            'destination_name',
        ]));

        // For example, you might save the trip to the database here
        // TripCreated::dispatch($newUserTrip, $request->user());
        broadcast(new TripCreated($newUserTrip, $request->user()));

        return response()->json(['message' => 'Trip created successfully', 'data' => $newUserTrip], 201);
    }

    public function show(Request $request, Trip $trip)
    {
        $trip->load('user');

        if($trip->user_id === $request->user()->id){
            return response()->json(['message' => 'Trip received', 'data' => $trip], 200);
        }else if($trip->driver_id && $request->user()->driver && $trip->driver_id === $request->user()->driver->id){
            return response()->json(['message' => 'Driver Trip received', 'data' => $trip], 200);
        }

        return response()->json(['message' => 'Can not get this trip'], 403);
    }

    public function acceptTrip(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location
        ]);

        $acceptedTrip = $trip->load('driver.user');

        // Call an event listener class, and dispatch the event
        // This will allow the event to be broadcasted to the frontend
        // This is useful for real-time updates, such as notifying the user that their trip has been accepted
        // The event will be handled by the TripAccepted event class
        // and the broadcastOn method will determine which channel to broadcast on
        
        TripAccepted::dispatch($acceptedTrip, $request->user());

        // In this case, we are broadcasting on a private channel
        // You can listen to this event on the frontend using Laravel Echo
        // and update the UI accordingly
        return response()->json(['message' => 'driver accepted trip at location:', 'data' => $acceptedTrip],200);
    }

    public function startTrip(Request $request, Trip $trip)
    {
        $tripStart = $trip->load('driver.user');

        TripStarted::dispatch($tripStart, $request->user());

        return response()->json(['message' => 'The user has been taken for the destination', 'data' => $tripStart],200);
    }
    
    public function endTrip(Request $request, Trip $trip)
    {
        $tripCompleted = $trip->load('driver.user');

        TripEnded::dispatch($tripCompleted, $request->user());

        return response()->json(['message' => 'The user has been dropped at the destination', 'data' => $tripCompleted],200);

    }
    
    public function updateLocation(Request $request, Trip $trip)
    {
        $request->validate([
            'driver_location' => 'required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        TripLocationUpdated::dispatch($trip, $request->user());

        return response()->json(['message' => 'Location updated'],200);
    }
}
