<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rack;

class RackSeeder extends Seeder
{
    public function run()
    {
        Rack::truncate();

        Rack::create(['name' => 'Rak A1']);
        Rack::create(['name' => 'Rak A2']);
        Rack::create(['name' => 'Rak B1']);
        Rack::create(['name' => 'Lemari Besi 01']);
    }
}