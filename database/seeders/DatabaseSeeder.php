<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Role::updateOrCreate(['name' => 'user', 'permissions' => []]);
        Role::updateOrCreate(['name' => 'admin', 'permissions' => ['*']]);

        foreach (config('available-settings') as $group => $settings) {
            foreach ($settings as $setting) {
                if (!isset($setting['default'])) {
                    continue;
                }
                Setting::firstOrCreate([
                    'key' => $setting['name'],
                    'value' => $setting['default'],
                ]);
            }
        }
    }
}
