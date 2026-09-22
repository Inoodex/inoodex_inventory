<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\LeadGenaration;
use Illuminate\Database\Seeder;
use Database\Seeders\ProductsSeeder;
use Database\Factories\LeadGenarationFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(ChartOfAccountSeeder::class);
        // $this->call(CategorySeeder::class);
        // $this->call(BrandSeeder::class);
        // $this->call(VendorSeeder::class);
        // $this->call(CustomerSeeder::class);
        // $this->call(ProductSeeder::class);
        // $this->call(PurchaseSeeder::class);
        // $this->call(SaleSeeder::class);
    }
}
