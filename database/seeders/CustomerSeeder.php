<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Rahman & Brothers Corporate',
                'phone' => '+8801811200001',
                'email' => 'contact@rahmanbrothers.com',
                'address' => 'Motijheel Commercial Area, Dhaka-1000',
            ],
            [
                'name' => 'TechMatrix Solutions Ltd',
                'phone' => '+8801811200002',
                'email' => 'admin@techmatrix-bd.com',
                'address' => 'House 14, Road 11, Banani, Dhaka-1213',
            ],
            [
                'name' => 'Blue Ocean Software Inc',
                'phone' => '+8801811200003',
                'email' => 'procurement@blueoceansoft.net',
                'address' => 'Level 5, Navana Tower, Gulshan-1, Dhaka-1212',
            ],
            [
                'name' => 'CyberSoft Technologies',
                'phone' => '+8801811200004',
                'email' => 'it@cybersoftbd.com',
                'address' => 'Sector 7, Road 18, Uttara, Dhaka-1230',
            ],
            [
                'name' => 'Prime Financial Consultants',
                'phone' => '+8801811200005',
                'email' => 'info@primefinancialbd.com',
                'address' => 'Dilkusha C/A, Dhaka-1000',
            ],
            [
                'name' => 'Zenith Media & Advertising',
                'phone' => '+8801811200006',
                'email' => 'studio@zenithmedia.com.bd',
                'address' => 'Tejgaon Industrial Area, Dhaka-1208',
            ],
            [
                'name' => 'Green Leaf Trading House',
                'phone' => '+8801811200007',
                'email' => 'support@greenleaftrading.com',
                'address' => 'Khatunganj Commercial Hub, Chattogram',
            ],
            [
                'name' => 'Crescent Hospital Admin IT',
                'phone' => '+8801811200008',
                'email' => 'hospital_it@crescenthealth.org',
                'address' => 'Dhanmondi 27, Dhaka-1209',
            ],
            [
                'name' => 'Delta Engineering Works',
                'phone' => '+8801811200009',
                'email' => 'delta_eng@deltaworks.com.bd',
                'address' => 'Tongij Industrial Belt, Gazipur',
            ],
            [
                'name' => 'Horizon Telecom Services',
                'phone' => '+8801811200010',
                'email' => 'ops@horizontelecom-bd.com',
                'address' => 'Mohakhali DOHS, Dhaka-1206',
            ],
            [
                'name' => 'Farhan Chowdhury',
                'phone' => '+8801811200011',
                'email' => 'farhan.chowdhury92@gmail.com',
                'address' => 'Flat 4B, Green Road, Dhanmondi, Dhaka',
            ],
            [
                'name' => 'Sadia Islam',
                'phone' => '+8801811200012',
                'email' => 'sadia.islam.arch@outlook.com',
                'address' => 'House 22, Road 4, Sector 4, Uttara, Dhaka',
            ],
            [
                'name' => 'Tanvir Ahmed',
                'phone' => '+8801811200013',
                'email' => 'tanvir.ahmed.dev@gmail.com',
                'address' => 'Mirpur DOHS, Road 9, Dhaka-1216',
            ],
            [
                'name' => 'Nusrat Jahan',
                'phone' => '+8801811200014',
                'email' => 'nusrat.jahan.bd@yahoo.com',
                'address' => 'Block C, Bashundhara R/A, Dhaka-1229',
            ],
            [
                'name' => 'Mahfuzur Rahman',
                'phone' => '+8801811200015',
                'email' => 'mahfuz.rahman77@gmail.com',
                'address' => 'Lalmatia Block D, Dhaka-1207',
            ],
            [
                'name' => 'Apex Logistics Ltd',
                'phone' => '+8801811200016',
                'email' => 'it@apexlogistics-bd.com',
                'address' => 'Agrabad Commercial Area, Chattogram',
            ],
            [
                'name' => 'Beacon International School',
                'phone' => '+8801811200017',
                'email' => 'accounts@beaconschool.edu.bd',
                'address' => 'Gulshan Avenue, Dhaka-1212',
            ],
            [
                'name' => 'Standard Agro Industries',
                'phone' => '+8801811200018',
                'email' => 'supply@standardagro.com',
                'address' => 'Joydebpur Road, Gazipur',
            ],
            [
                'name' => 'Orion Pharmaceuticals IT Dept',
                'phone' => '+8801811200019',
                'email' => 'orion_it@orionpharma.com',
                'address' => 'Orion House, 153-154 Tejgaon I/A, Dhaka',
            ],
            [
                'name' => 'City View Residency Office',
                'phone' => '+8801811200020',
                'email' => 'management@cityviewresidency.com',
                'address' => 'Kakrail VIP Road, Dhaka-1000',
            ],
        ];

        foreach ($customers as $c) {
            Customer::firstOrCreate(
                ['phone' => $c['phone']],
                [
                    'name' => $c['name'],
                    'email' => $c['email'],
                    'address' => $c['address'],
                    'status' => '1',
                    'is_verified' => true,
                ]
            );
        }
    }
}
