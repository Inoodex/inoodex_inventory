<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Tech Data Bangladesh Ltd',
                'phone' => '+8801711000101',
                'email' => 'sales@techdata-bd.com',
                'address' => 'IDB Bhaban, 4th Floor, Agargaon, Dhaka-1207',
                'status' => '1',
            ],
            [
                'name' => 'Smart Technologies (BD) Ltd',
                'phone' => '+8801711000102',
                'email' => 'info@smart-bd.com',
                'address' => 'Jahangir Tower, 10 Kawran Bazar, Dhaka-1215',
                'status' => '1',
            ],
            [
                'name' => 'Global Brand PLC',
                'phone' => '+8801711000103',
                'email' => 'contact@globalbrand.com.bd',
                'address' => '19/2 West Panthapath, Dhanmondi, Dhaka-1205',
                'status' => '1',
            ],
            [
                'name' => 'UCC (Unique Computer Center)',
                'phone' => '+8801711000104',
                'email' => 'sales@ucc.com.bd',
                'address' => 'Multiplan Center, Level 6, Elephant Road, Dhaka-1205',
                'status' => '1',
            ],
            [
                'name' => 'Star Tech Distro Hub',
                'phone' => '+8801711000105',
                'email' => 'distribution@startech.com.bd',
                'address' => '9/1 Pragati Sarani, Kuril, Dhaka-1229',
                'status' => '1',
            ],
            [
                'name' => 'Flora Computer Wholesale',
                'phone' => '+8801711000106',
                'email' => 'wholesale@flora-bd.com',
                'address' => '119-120 Motijheel C/A, Dhaka-1000',
                'status' => '1',
            ],
            [
                'name' => 'Computer Source Ltd',
                'phone' => '+8801711000107',
                'email' => 'supply@computersourcebd.com',
                'address' => 'House 49, Road 9/A, Dhanmondi, Dhaka-1209',
                'status' => '1',
            ],
            [
                'name' => 'Excel Telecom & IT Solutions',
                'phone' => '+8801711000108',
                'email' => 'corporate@exceltelecom.com',
                'address' => 'Gulshan Center Point, Gulshan-2, Dhaka-1212',
                'status' => '1',
            ],
            [
                'name' => 'South City Tech Traders',
                'phone' => '+8801711000109',
                'email' => 'info@southcitytech.com',
                'address' => 'Shop 214, BCS Computer City, Agargaon, Dhaka',
                'status' => '1',
            ],
            [
                'name' => 'Nexus Distribution Network',
                'phone' => '+8801711000110',
                'email' => 'distro@nexus-bd.com',
                'address' => 'Banani Super Market, Level 3, Banani, Dhaka-1213',
                'status' => '1',
            ],
            [
                'name' => 'Apex Silicon Importers',
                'phone' => '+8801711000111',
                'email' => 'sales@apexsilicon.com',
                'address' => 'Agrabad C/A, Chattogram-4100',
                'status' => '1',
            ],
            [
                'name' => 'Silicon Valley BD Supply',
                'phone' => '+8801711000112',
                'email' => 'orders@siliconvalleybd.net',
                'address' => 'Level 8, Eastern Plus Shopping Complex, Shantinagar, Dhaka',
                'status' => '1',
            ],
            [
                'name' => 'Horizon Digital Logistics',
                'phone' => '+8801711000113',
                'email' => 'logistics@horizondigital.com',
                'address' => 'Plot 12, Sector 3, Uttara, Dhaka-1230',
                'status' => '1',
            ],
            [
                'name' => 'Vertex IT Distributors',
                'phone' => '+8801711000114',
                'email' => 'support@vertexit-bd.com',
                'address' => 'Suvastu Arcade, New Elephant Road, Dhaka-1205',
                'status' => '1',
            ],
            [
                'name' => 'Metro Computer Supplies',
                'phone' => '+8801711000115',
                'email' => 'metro@metrocomputerbd.com',
                'address' => 'Alpana Plaza, 51 Elephant Road, Dhaka-1205',
                'status' => '1',
            ],
            [
                'name' => 'Pioneer Micro Systems',
                'phone' => '+8801711000116',
                'email' => 'sales@pioneermicro.com',
                'address' => 'GEC Circle, Nasirabad, Chattogram',
                'status' => '1',
            ],
            [
                'name' => 'Orient Tech Hub',
                'phone' => '+8801711000117',
                'email' => 'info@orienttechhub.com',
                'address' => 'Chawkbazar Main Road, Cumilla',
                'status' => '1',
            ],
            [
                'name' => 'Microtech Components Ltd',
                'phone' => '+8801711000118',
                'email' => 'supply@microtech-bd.com',
                'address' => 'Zindabazar Commercial Area, Sylhet',
                'status' => '1',
            ],
            [
                'name' => 'Delta Network Hardware',
                'phone' => '+8801711000119',
                'email' => 'sales@deltanetwork-bd.com',
                'address' => 'Bogra IT Park, Sherpur Road, Bogura',
                'status' => '1',
            ],
            [
                'name' => 'Alpha Infotech Supplies',
                'phone' => '+8801711000120',
                'email' => 'inquiry@alphainfotech.com',
                'address' => 'Rajshahi Commercial Plaza, Shaheb Bazar, Rajshahi',
                'status' => '1',
            ],
        ];

        foreach ($vendors as $v) {
            Vendor::firstOrCreate(
                ['name' => $v['name']],
                [
                    'phone' => $v['phone'],
                    'email' => $v['email'],
                    'address' => $v['address'],
                    'status' => $v['status'],
                ]
            );
        }
    }
}
