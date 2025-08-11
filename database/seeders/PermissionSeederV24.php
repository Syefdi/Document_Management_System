<?php

namespace Database\Seeders;

use App\Models\Actions;
use App\Models\RoleClaims;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Users;

class PermissionSeederV24 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $action = Actions::where('name', '=', 'Manage Sharable Link')->first();

        if ($action == null) {

            $user = Users::first();

            $actions =
                [
                    [
                        'id' => 'f9ec1096-b798-4623-bbf8-4f5d4fe775e9',
                        'name' => 'Manage Sharable Link',
                        'order' => 10,
                        'pageId' => 'eddf9e8e-0c70-4cde-b5f9-117a879747d6',
                        'code' => 'ALL_DOCUMENTS_MANAGE_SHARABLE_LINK',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ],
                    [
                        'id' => 'e9ff854b-23f7-46c2-9029-efba3d8587b5',
                        'name' => 'Manage Sharable Link',
                        'order' => 7,
                        'pageId' => 'fc97dc8f-b4da-46b1-a179-ab206d8b7efd',
                        'code' => 'ASSIGNED_DOCUMENTS_MANAGE_SHARABLE_LINK',
                        'createdBy' => $user->id,
                        'modifiedBy' => $user->id,
                        'isDeleted' => 0
                    ]
                ];

            $roleClaims = [
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'f9ec1096-b798-4623-bbf8-4f5d4fe775e9',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ALL_DOCUMENTS_MANAGE_SHARABLE_LINK',
                ],
                [
                    'id' => Str::uuid(36),
                    'actionId' => 'e9ff854b-23f7-46c2-9029-efba3d8587b5',
                    'roleId' => 'f8b6ace9-a625-4397-bdf8-f34060dbd8e4',
                    'claimType' => 'ASSIGNED_DOCUMENTS_MANAGE_SHARABLE_LINK'
                ]
            ];

            $updatedActions =  collect($actions)->map(function ($item, $key) {
                $item['createdDate'] = Carbon::now();
                $item['modifiedDate'] = Carbon::now();
                return $item;
            });
            Actions::insert($updatedActions->toArray());
            RoleClaims::insert($roleClaims);
        }
    }
}
