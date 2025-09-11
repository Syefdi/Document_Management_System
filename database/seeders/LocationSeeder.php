<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run()
    {
        Location::truncate();

        Location::create(['name' => 'Gudang Utama']);
        Location::create(['name' => 'Lantai 1']);
        Location::create(['name' => 'Lantai 2']);
        Location::create(['name' => 'Ruang Arsip']);
    }
}