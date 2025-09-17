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
            'name' => 'required|string|max:255|unique:racks,name,NULL,id,isDeleted,0',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
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
            'name' => 'required|string|max:255|unique:racks,name,' . $id . ',id,isDeleted,0',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
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