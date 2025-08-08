<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    
    public function show(Request $request)
    {
        $user = $request->user();
        $user->load('driver'); // Assuming 'driver' is a relationship defined in the User model

        return response()->json(['message' => 'Driver booked', 'data' => $user], 200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'year' => 'required|numeric|between:2010,2025',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'license_plate' => [
                'required',
                'string',
                'max:20',
                Rule::unique('drivers')->ignore($request->user()->id, 'user_id'),
            ],
            'name' => 'required|string|max:255'
        ]);

        $user = $request->user();
        $user->update($request->only(['name']));
        //
        // $driver = $user->driver;

        // if ($driver) {
        //     $driver->update($request->only(['year', 'make', 'model', 'color', 'license_plate']));
        // } else {
        //     return response()->json(['error' => 'Driver record not found'], 404);
        // }
        //
        $user->driver()->updateOrCreate(['user_id' => $user->id],$request->only([
            'year', 'make', 'model', 'color', 'license_plate'
        ]));

        $user->load('driver');
        return response()->json(['message' => 'Driver data updated', 'data' => $user], 200);
    }

}
