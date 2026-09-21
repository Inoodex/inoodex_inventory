<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laptops & Notebooks',
                'description' => 'Business, ultrabook, and gaming laptops from top manufacturers.',
                'order_by' => 1,
            ],
            [
                'name' => 'Desktop Computers & All-in-One',
                'description' => 'Prebuilt brand desktops, workstations, and AIO systems.',
                'order_by' => 2,
            ],
            [
                'name' => 'Computer Processors (CPUs)',
                'description' => 'Intel and AMD multi-core desktop and workstation processors.',
                'order_by' => 3,
            ],
            [
                'name' => 'Graphics Cards (GPUs)',
                'description' => 'Dedicated graphics cards for gaming, 3D rendering, and AI computing.',
                'order_by' => 4,
            ],
            [
                'name' => 'Motherboards',
                'description' => 'Intel and AMD socket motherboards with modern chipset support.',
                'order_by' => 5,
            ],
            [
                'name' => 'RAM & Memory Modules',
                'description' => 'DDR4 and DDR5 desktop and laptop RAM modules.',
                'order_by' => 6,
            ],
            [
                'name' => 'Solid State Drives (SSDs)',
                'description' => 'High-speed NVMe PCIe M.2 and SATA SSD internal storage.',
                'order_by' => 7,
            ],
            [
                'name' => 'Hard Disk Drives (HDDs)',
                'description' => 'High-capacity 3.5-inch desktop and 2.5-inch surveillance hard drives.',
                'order_by' => 8,
            ],
            [
                'name' => 'Power Supply Units (PSU)',
                'description' => '80 Plus Bronze, Gold, and Platinum certified power supplies.',
                'order_by' => 9,
            ],
            [
                'name' => 'Computer Monitors',
                'description' => 'Full HD, 2K, 4K, IPS, and high-refresh gaming display monitors.',
                'order_by' => 10,
            ],
            [
                'name' => 'Keyboards & Mice',
                'description' => 'Ergonomic, wireless, and mechanical RGB input peripherals.',
                'order_by' => 11,
            ],
            [
                'name' => 'Networking Routers & Switches',
                'description' => 'Wi-Fi 6 routers, gigabit switches, and wireless access points.',
                'order_by' => 12,
            ],
            [
                'name' => 'Printers & Scanners',
                'description' => 'Laser, all-in-one inkjet printers, and document scanners.',
                'order_by' => 13,
            ],
            [
                'name' => 'Security Cameras & CCTV',
                'description' => 'IP cameras, HD dome cameras, NVRs, and surveillance accessories.',
                'order_by' => 14,
            ],
            [
                'name' => 'Audio & Headphones',
                'description' => 'Studio monitors, noise-canceling headsets, and conference speakers.',
                'order_by' => 15,
            ],
            [
                'name' => 'UPS & Power Backup',
                'description' => 'Offline and online high-capacity UPS power protection units.',
                'order_by' => 16,
            ],
            [
                'name' => 'Server Hardware & Racks',
                'description' => 'Enterprise rackmount servers, server chassis, and accessories.',
                'order_by' => 17,
            ],
            [
                'name' => 'Thermal Solutions & Cooling',
                'description' => 'AIO liquid coolers, CPU air coolers, and chassis case fans.',
                'order_by' => 18,
            ],
            [
                'name' => 'Computer Cases & Chassis',
                'description' => 'Mid-tower, full-tower, and mini-ITX tempered glass PC cases.',
                'order_by' => 19,
            ],
            [
                'name' => 'Projectors & Presentation Displays',
                'description' => 'Laser, DLP multimedia conference projectors and display screens.',
                'order_by' => 20,
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'status' => true,
                    'order_by' => $cat['order_by'],
                ]
            );
        }
    }
}
