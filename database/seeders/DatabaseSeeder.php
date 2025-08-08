<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => (string) Str::uuid(), // generate UUID
            'firstName' => 'admin',
            'lastName' => '',
            'isDeleted' => 0,
            'userName' => 'admin',
            'normalizedUserName' => 'ADMIN',
            'email' => 'admin@admin.com',
            'normalizedEmail' => 'ADMIN@ADMIN.COM',
            'emailConfirmed' => 1,
            'password' => Hash::make('123456'),
            'securityStamp' => (string) Str::uuid(),
            'concurrencyStamp' => (string) Str::uuid(),
            'phoneNumber' => null,
            'phoneNumberConfirmed' => 0,
            'twoFactorEnabled' => 0,
            'lockoutEnd' => null,
            'lockoutEnabled' => 0,
            'accessFailedCount' => 0,
            'resetPasswordCode' => null,
        ]);
<<<<<<< HEAD

=======
>>>>>>> 6895172fd2f31385a5c656d4e4aa7daeb185abfc
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            LanguageSeeder::class,
            PermissionSeederV2::class,
            PermissionSeederV21::class,
            PermissionSeederV22::class,
            PermissionSeederV23::class,
            PermissionSeederV24::class,
            PermissionSeederV30::class,
            PermissionSeederV31::class,
            PermissionSeederV40::class,
            PermissionSeederV50::class
        ]);
    }
}
