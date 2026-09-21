<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productsData = [
            [
                'name' => 'ASUS ROG Zephyrus G14 Gaming Laptop',
                'category' => 'Laptops & Notebooks',
                'brand' => 'ASUS',
                'model' => 'GA403UI-QS024W',
                'barcode' => '4711081982301',
                'warranty' => 730, // 2 Years
                'is_serialized' => 1,
                'stock' => 8,
                'serial_prefix' => 'ROG-G14',
            ],
            [
                'name' => 'Dell Latitude 5540 Business Laptop',
                'category' => 'Laptops & Notebooks',
                'brand' => 'Dell',
                'model' => 'LAT-5540-I7',
                'barcode' => '5397184820102',
                'warranty' => 1095, // 3 Years
                'is_serialized' => 1,
                'stock' => 12,
                'serial_prefix' => 'DLL-5540',
            ],
            [
                'name' => 'HP ProBook 450 G10 Laptop',
                'category' => 'Laptops & Notebooks',
                'brand' => 'HP',
                'model' => 'PB-450-G10',
                'barcode' => '1974971230103',
                'warranty' => 730,
                'is_serialized' => 1,
                'stock' => 15,
                'serial_prefix' => 'HP-450G10',
            ],
            [
                'name' => 'Apple MacBook Air 15" M3 Chip',
                'category' => 'Laptops & Notebooks',
                'brand' => 'Apple',
                'model' => 'MXD13LL/A',
                'barcode' => '1959491190104',
                'warranty' => 365,
                'is_serialized' => 1,
                'stock' => 6,
                'serial_prefix' => 'APL-M3AIR',
            ],
            [
                'name' => 'Intel Core i7-14700K 20-Core Desktop Processor',
                'category' => 'Computer Processors (CPUs)',
                'brand' => 'Intel',
                'model' => 'BX8071514700K',
                'barcode' => '5032037278105',
                'warranty' => 1095,
                'is_serialized' => 1,
                'stock' => 20,
                'serial_prefix' => 'INT-14700K',
            ],
            [
                'name' => 'AMD Ryzen 7 7800X3D 8-Core Gaming Processor',
                'category' => 'Computer Processors (CPUs)',
                'brand' => 'AMD',
                'model' => '100-100000910WOF',
                'barcode' => '7301433149106',
                'warranty' => 1095,
                'is_serialized' => 1,
                'stock' => 18,
                'serial_prefix' => 'AMD-7800X3D',
            ],
            [
                'name' => 'ASUS ROG Strix GeForce RTX 4070 Ti SUPER 16GB OC',
                'category' => 'Graphics Cards (GPUs)',
                'brand' => 'ASUS',
                'model' => 'ROG-STRIX-RTX4070TIS-O16G',
                'barcode' => '4711387472107',
                'warranty' => 1095,
                'is_serialized' => 1,
                'stock' => 7,
                'serial_prefix' => 'NV-4070TIS',
            ],
            [
                'name' => 'MSI GeForce RTX 4060 Ventus 2X Black 8GB OC',
                'category' => 'Graphics Cards (GPUs)',
                'brand' => 'MSI',
                'model' => 'RTX-4060-VENTUS-2X-8G-OC',
                'barcode' => '4719072973108',
                'warranty' => 1095,
                'is_serialized' => 1,
                'stock' => 14,
                'serial_prefix' => 'MSI-4060V',
            ],
            [
                'name' => 'Gigabyte B760M AORUS ELITE AX Motherboard',
                'category' => 'Motherboards',
                'brand' => 'Gigabyte',
                'model' => 'B760M-AORUS-ELITE-AX',
                'barcode' => '4719331851109',
                'warranty' => 1095,
                'is_serialized' => 1,
                'stock' => 10,
                'serial_prefix' => 'GB-B760M',
            ],
            [
                'name' => 'Corsair Vengeance RGB 32GB (2x16GB) DDR5 6000MHz',
                'category' => 'RAM & Memory Modules',
                'brand' => 'Corsair',
                'model' => 'CMH32GX5M2B6000C30',
                'barcode' => '8400066991110',
                'warranty' => 1825, // Lifetime/5 Years
                'is_serialized' => 0,
                'stock' => 35,
                'serial_prefix' => null,
            ],
            [
                'name' => 'Kingston FURY Beast 16GB DDR4 3200MHz RAM',
                'category' => 'RAM & Memory Modules',
                'brand' => 'Kingston',
                'model' => 'KF432C16BB/16',
                'barcode' => '7406173191111',
                'warranty' => 1825,
                'is_serialized' => 0,
                'stock' => 50,
                'serial_prefix' => null,
            ],
            [
                'name' => 'Samsung 990 PRO 2TB NVMe M.2 PCIe 4.0 SSD',
                'category' => 'Solid State Drives (SSDs)',
                'brand' => 'Samsung',
                'model' => 'MZ-V9P2T0B/AM',
                'barcode' => '8872767071112',
                'warranty' => 1825,
                'is_serialized' => 1,
                'stock' => 25,
                'serial_prefix' => 'SAM-990P',
            ],
            [
                'name' => 'Western Digital WD Blue SA510 1TB SATA 2.5" SSD',
                'category' => 'Solid State Drives (SSDs)',
                'brand' => 'Western Digital',
                'model' => 'WDS100T3B0A',
                'barcode' => '7180378871113',
                'warranty' => 1095,
                'is_serialized' => 0,
                'stock' => 40,
                'serial_prefix' => null,
            ],
            [
                'name' => 'Seagate Barracuda 2TB 3.5" 7200RPM Desktop HDD',
                'category' => 'Hard Disk Drives (HDDs)',
                'brand' => 'Seagate',
                'model' => 'ST2000DM008',
                'barcode' => '7636491101114',
                'warranty' => 730,
                'is_serialized' => 0,
                'stock' => 30,
                'serial_prefix' => null,
            ],
            [
                'name' => 'Corsair RM750e 750W 80+ Gold Fully Modular PSU',
                'category' => 'Power Supply Units (PSU)',
                'brand' => 'Corsair',
                'model' => 'CP-9020262-NA',
                'barcode' => '8400066601115',
                'warranty' => 2555, // 7 Years
                'is_serialized' => 1,
                'stock' => 16,
                'serial_prefix' => 'CSR-RM750',
            ],
            [
                'name' => 'LG UltraGear 27" QHD 165Hz IPS Gaming Monitor',
                'category' => 'Computer Monitors',
                'brand' => 'LG Electronics',
                'model' => '27GR75Q-B',
                'barcode' => '8806091871116',
                'warranty' => 1095,
                'is_serialized' => 1,
                'stock' => 9,
                'serial_prefix' => 'LG-27QHD',
            ],
            [
                'name' => 'Logitech MX Master 3S Advanced Wireless Mouse',
                'category' => 'Keyboards & Mice',
                'brand' => 'Logitech',
                'model' => '910-006557',
                'barcode' => '0978551731117',
                'warranty' => 365,
                'is_serialized' => 1,
                'stock' => 22,
                'serial_prefix' => 'LOG-MX3S',
            ],
            [
                'name' => 'Logitech G213 Prodigy RGB Gaming Keyboard',
                'category' => 'Keyboards & Mice',
                'brand' => 'Logitech',
                'model' => '920-008084',
                'barcode' => '0978551221118',
                'warranty' => 730,
                'is_serialized' => 0,
                'stock' => 28,
                'serial_prefix' => null,
            ],
            [
                'name' => 'TP-Link Archer AX73 AX5400 Dual-Band Wi-Fi 6 Router',
                'category' => 'Networking Routers & Switches',
                'brand' => 'TP-Link',
                'model' => 'Archer AX73',
                'barcode' => '6935364011119',
                'warranty' => 730,
                'is_serialized' => 1,
                'stock' => 15,
                'serial_prefix' => 'TPL-AX73',
            ],
            [
                'name' => 'Dahua 4MP WizSense IR Eyeball IP Camera',
                'category' => 'Security Cameras & CCTV',
                'brand' => 'Dahua',
                'model' => 'DH-IPC-HDW2441T-S',
                'barcode' => '6923172511120',
                'warranty' => 730,
                'is_serialized' => 1,
                'stock' => 30,
                'serial_prefix' => 'DH-4MP',
            ],
        ];

        foreach ($productsData as $data) {
            $category = Category::where('name', $data['category'])->first();
            $brand = Brand::where('name', $data['brand'])->first();

            $product = Product::firstOrCreate(
                ['model' => $data['model']],
                [
                    'name' => $data['name'],
                    'category_id' => $category?->id,
                    'brand_id' => $brand?->id,
                    'barcode' => $data['barcode'],
                    'warranty' => $data['warranty'],
                    'is_serialized' => $data['is_serialized'],
                    'status' => '1',
                ]
            );

            // Seed inventory stock
            $inventory = Inventory::firstOrCreate(
                ['product_id' => $product->id],
                [
                    'opening_stock' => $data['stock'],
                    'current_stock' => $data['stock'],
                    'notes' => 'Initial seeded opening inventory',
                ]
            );

            // If product is serialized, seed physical unit serials matching stock
            if ($data['is_serialized'] && $data['serial_prefix']) {
                $existingSerialsCount = ProductSerial::where('product_id', $product->id)->count();
                $serialsToGenerate = max(0, min($data['stock'], 6) - $existingSerialsCount);

                for ($i = 1; $i <= $serialsToGenerate; $i++) {
                    $serialNum = $data['serial_prefix'] . '-' . str_pad((string)($existingSerialsCount + $i), 4, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(4));
                    ProductSerial::firstOrCreate(
                        ['serial_number' => $serialNum],
                        [
                            'product_id' => $product->id,
                            'status' => 'available',
                        ]
                    );
                }
            }
        }
    }
}
