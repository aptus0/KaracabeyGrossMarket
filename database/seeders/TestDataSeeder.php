<?php

namespace Database\Seeders;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'),
            ]
        );

        // Register test device tokens
        DeviceToken::updateOrCreate(
            ['token' => 'test_ios_token_12345'],
            [
                'user_id' => $user->id,
                'device_type' => 'ios',
                'device_name' => 'Test iPhone',
                'is_active' => true,
            ]
        );

        DeviceToken::updateOrCreate(
            ['token' => 'test_android_token_67890'],
            [
                'user_id' => $user->id,
                'device_type' => 'android',
                'device_name' => 'Test Android',
                'is_active' => true,
            ]
        );

        $this->command->info("✓ Test data created");
        $this->command->info("  User: {$user->email} (ID: {$user->id})");
        $this->command->info("  Device Tokens: 2");
    }
}
