<?php

namespace App\Http\Controllers; 

use App\Models\Location;
use App\Models\Rack;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class MasterDataController extends Controller
{
    public function getLocations()
    {
        return Location::orderBy('name')->get();
    }

    public function getLocation($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        return response()->json($location);
    }

    public function createLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate name
        $existingLocation = Location::where('name', $request->name)->first();
        if ($existingLocation) {
            return response()->json([
                'message' => 'Location name already exists',
                'errors' => ['name' => ['Location name already exists']]
            ], 422);
        }

        $location = new Location();
        $location->id = Uuid::uuid4()->toString();
        $location->name = $request->name;
        $location->description = $request->description;
        $location->address = $request->address;
        $location->save();

        return response()->json($location, 201);
    }

    public function updateLocation(Request $request, $id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate name (excluding current location)
        $existingLocation = Location::where('name', $request->name)
                                  ->where('id', '!=', $id)
                                  ->first();
        if ($existingLocation) {
            return response()->json([
                'message' => 'Location name already exists',
                'errors' => ['name' => ['Location name already exists']]
            ], 422);
        }

        $location->name = $request->name;
        $location->description = $request->description;
        $location->address = $request->address;
        $location->save();

        return response()->json($location);
    }

    public function deleteLocation($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }

        $userId = Auth::parseToken()->getPayload()->get('userId');
        $location->isDeleted = true;
        $location->deletedBy = $userId;
        $location->deleted_at = now();
        $location->save();
        
        return response()->json(['message' => 'Location deleted successfully']);
    }

    public function getRacks()
    {
        return Rack::orderBy('name')->get();
    }

    public function getRack($id)
    {
        $rack = Rack::find($id);
        if (!$rack) {
            return response()->json(['message' => 'Rack not found'], 404);
        }
        return response()->json($rack);
    }

    public function createRack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate name
        $existingRack = Rack::where('name', $request->name)->first();
        if ($existingRack) {
            return response()->json([
                'message' => 'Rack name already exists',
                'errors' => ['name' => ['Rack name already exists']]
            ], 422);
        }

        $rack = new Rack();
        $rack->id = Uuid::uuid4()->toString();
        $rack->name = $request->name;
        $rack->description = $request->description;
        $rack->save();

        return response()->json($rack, 201);
    }

    public function updateRack(Request $request, $id)
    {
        $rack = Rack::find($id);
        if (!$rack) {
            return response()->json(['message' => 'Rack not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check for duplicate name (excluding current rack)
        $existingRack = Rack::where('name', $request->name)
                           ->where('id', '!=', $id)
                           ->first();
        if ($existingRack) {
            return response()->json([
                'message' => 'Rack name already exists',
                'errors' => ['name' => ['Rack name already exists']]
            ], 422);
        }

        $rack->name = $request->name;
        $rack->description = $request->description;
        $rack->save();

        return response()->json($rack);
    }

    public function deleteRack($id)
    {
        $rack = Rack::find($id);
        if (!$rack) {
            return response()->json(['message' => 'Rack not found'], 404);
        }

        $userId = Auth::parseToken()->getPayload()->get('userId');
        $rack->isDeleted = true;
        $rack->deletedBy = $userId;
        $rack->deleted_at = now();
        $rack->save();
        
        return response()->json(['message' => 'Rack deleted successfully']);
    }
}