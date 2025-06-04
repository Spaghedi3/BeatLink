<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $adminEmail = 'admin@example.com';

        $admin = User::firstOrNew(['email' => $adminEmail]);
        $admin->username     = 'SiteAdministrator';
        $admin->password = Hash::make('parolamea');
        $admin->is_admin = true;
        $admin->save();
    }
}
