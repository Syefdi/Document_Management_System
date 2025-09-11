<?php

namespace App\Http\Controllers; 

use App\Models\Location;
use App\Models\Rack;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function getLocations()
    {
        return Location::orderBy('name')->get();
    }

    public function getRacks()
    {
        return Rack::orderBy('name')->get();
    }
}