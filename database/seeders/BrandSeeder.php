<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'ASUS',
            'Dell',
            'HP',
            'Lenovo',
            'Apple',
            'Samsung',
            'Logitech',
            'TP-Link',
            'Cisco',
            'MSI',
            'Intel',
            'AMD',
            'Kingston',
            'Corsair',
            'Western Digital',
            'Seagate',
            'Dahua',
            'Hikvision',
            'Gigabyte',
            'LG Electronics',
        ];

        foreach ($brands as $name) {
            Brand::firstOrCreate(
                ['name' => $name],
                ['status' => 1]
            );
        }
    }
}
