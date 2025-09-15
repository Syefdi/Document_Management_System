<?php

namespace Database\Seeders;

use App\Models\Actions;
use App\Models\Pages;
use App\Models\RoleClaims;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Users;

class LocationRackPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locationPage = Pages::where('name', '=', 'Location')->first();
        $rackPage = Pages::where('name', '=', 'Rack')->first();

        if ($locationPage == null || $rackPage == null) {

            $user = Users::first();

            $pages = [];
            $actions = [];
            $roleClaims = [];

            // Create Location page if not exists
            if ($locationPage == null) {
                $locationPageId = Str::uuid();
                $pages[] = [
                    'id' => $locationPageId,
                    'name' => 'Location',
                    'order' => 11,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ];

                // Location actions
                $locationActionId = Str::uuid();
                $actions[] = [
                    'id' => $locationActionId,
                    'name' => 'Manage Locations',
                    'order' => 1,
                    'pageId' => $locationPageId,
                    'code' => 'LOCATION_MANAGE_LOCATIONS',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ];

                // Location role claims
                $roleClaims[] = [
                    'id' => Str::uuid(),
                    'actionId' => $locationActionId,
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4', // Admin role
                    'claimType' => 'LOCATION_MANAGE_LOCATIONS',
                ];
            }

            // Create Rack page if not exists
            if ($rackPage == null) {
                $rackPageId = Str::uuid();
                $pages[] = [
                    'id' => $rackPageId,
                    'name' => 'Rack',
                    'order' => 12,
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ];

                // Rack actions
                $rackActionId = Str::uuid();
                $actions[] = [
                    'id' => $rackActionId,
                    'name' => 'Manage Racks',
                    'order' => 1,
                    'pageId' => $rackPageId,
                    'code' => 'RACK_MANAGE_RACKS',
                    'createdBy' => $user->id,
                    'modifiedBy' => $user->id,
                    'isDeleted' => 0
                ];

                // Rack role claims
                $roleClaims[] = [
                    'id' => Str::uuid(),
                    'actionId' => $rackActionId,
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4', // Admin role
                    'claimType' => 'RACK_MANAGE_RACKS',
                ];
            }

            // Insert data if there are any
            if (!empty($pages)) {
                $updatedPages = collect($pages)->map(function ($item) {
                    $item['createdDate'] = Carbon::now();
                    $item['modifiedDate'] = Carbon::now();
                    return $item;
                });
                Pages::insert($updatedPages->toArray());
            }

            if (!empty($actions)) {
                $updatedActions = collect($actions)->map(function ($item) {
                    $item['createdDate'] = Carbon::now();
                    $item['modifiedDate'] = Carbon::now();
                    return $item;
                });
                Actions::insert($updatedActions->toArray());
            }

            if (!empty($roleClaims)) {
                RoleClaims::insert($roleClaims);
            }
        }
    }
}