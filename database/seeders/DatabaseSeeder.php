<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PermissionSeeder::class,
            RoleTableSeeder::class,
            ModelRoleSeeder::class,
            RolePermissionSeeder::class,
            CategoryTableSeeder::class,
            PageSeeder::class,
            HomeSeeder::class,
            RoleEmailTypeSeeder::class,
            EmailTemplateSeeder::class,
            ReviewerCatSeeder::class,
        ]);
    }
}
