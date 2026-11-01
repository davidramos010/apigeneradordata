<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRateLimitOverride;
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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);
        
        User::factory()->create([
            'name' => 'Test admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Api User',
            'email' => 'apiservice@generardordata.com',
            'role' => 'user',
            'password' => '4piS3rv1c3P4ssw0rd.',
        ]);

        $boostedUser = User::factory()->create([
            'name' => 'Boosted Quota User',
            'email' => 'boosted@example.com',
            'role' => 'user',
        ]);

        UserRateLimitOverride::create([
            'user_id' => $boostedUser->id,
            'daily_limit' => 5000,
            'unlimited' => false,
            'notes' => 'Seeded example of a custom daily limit override',
        ]);

        $unlimitedUser = User::factory()->create([
            'name' => 'Unlimited Quota User',
            'email' => 'unlimited@example.com',
            'role' => 'user',
        ]);

        UserRateLimitOverride::create([
            'user_id' => $unlimitedUser->id,
            'daily_limit' => null,
            'unlimited' => true,
            'notes' => 'Seeded example of an unlimited override',
        ]);
    }
}
