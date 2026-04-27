<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MassProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = \App\Models\Tenant::first()->id ?? 1;
        // Chunking the creation to avoid memory limits
        for ($i = 0; $i < 20; $i++) {
            \App\Models\Product::factory(1000)->create(['tenant_id' => $tenantId]);
        }
    }
}
