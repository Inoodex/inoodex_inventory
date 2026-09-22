<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds for 10 demo purchases.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin?->id ?? 1;

        $purchasesData = [
            [
                'product_model' => 'GA403UI-QS024W', // ASUS ROG G14
                'vendor_name'   => 'Tech Data Bangladesh Ltd',
                'quantity'      => 3,
                'unit_price'    => 185000.00,
                'payment'       => 555000.00,
                'created_at'    => now()->subDays(25),
            ],
            [
                'product_model' => 'LAT-5540-I7', // Dell Latitude 5540
                'vendor_name'   => 'Smart Technologies (BD) Ltd',
                'quantity'      => 4,
                'unit_price'    => 110000.00,
                'payment'       => 300000.00,
                'created_at'    => now()->subDays(22),
            ],
            [
                'product_model' => 'PB-450-G10', // HP ProBook 450
                'vendor_name'   => 'Global Brand PLC',
                'quantity'      => 5,
                'unit_price'    => 85000.00,
                'payment'       => 425000.00,
                'created_at'    => now()->subDays(20),
            ],
            [
                'product_model' => 'MXD13LL/A', // Apple MacBook Air 15"
                'vendor_name'   => 'Flora Computer Wholesale',
                'quantity'      => 2,
                'unit_price'    => 175000.00,
                'payment'       => 350000.00,
                'created_at'    => now()->subDays(18),
            ],
            [
                'product_model' => 'LS28BG700EWXXL', // Samsung Odyssey G7
                'vendor_name'   => 'UCC (Unique Computer Center)',
                'quantity'      => 4,
                'unit_price'    => 65000.00,
                'payment'       => 200000.00,
                'created_at'    => now()->subDays(15),
            ],
            [
                'product_model' => '27UP850N-W', // LG UltraFine 27"
                'vendor_name'   => 'Star Tech Distro Hub',
                'quantity'      => 3,
                'unit_price'    => 48000.00,
                'payment'       => 144000.00,
                'created_at'    => now()->subDays(12),
            ],
            [
                'product_model' => 'BX8071514700K', // Intel Core i7-14700K
                'vendor_name'   => 'Computer Source Ltd',
                'quantity'      => 5,
                'unit_price'    => 42000.00,
                'payment'       => 210000.00,
                'created_at'    => now()->subDays(10),
            ],
            [
                'product_model' => '100-100000910WOF', // AMD Ryzen 7 7800X3D
                'vendor_name'   => 'UCC (Unique Computer Center)',
                'quantity'      => 4,
                'unit_price'    => 45000.00,
                'payment'       => 100000.00,
                'created_at'    => now()->subDays(7),
            ],
            [
                'product_model' => 'MZ-V9P2T0B/AM', // Samsung 990 PRO 2TB
                'vendor_name'   => 'Tech Data Bangladesh Ltd',
                'quantity'      => 8,
                'unit_price'    => 18500.00,
                'payment'       => 148000.00,
                'created_at'    => now()->subDays(5),
            ],
            [
                'product_model' => 'C1000-24T-4G-L', // Cisco Catalyst 1000
                'vendor_name'   => 'Smart Technologies (BD) Ltd',
                'quantity'      => 2,
                'unit_price'    => 52000.00,
                'payment'       => 104000.00,
                'created_at'    => now()->subDays(2),
            ],
        ];

        $allProducts = Product::all();
        $allVendors = Vendor::all();

        foreach ($purchasesData as $index => $data) {
            $purchaseNo = 'PUR-' . str_pad((string)($index + 1), 5, '0', STR_PAD_LEFT);

            $product = Product::where('model', $data['product_model'])->first() 
                ?? $allProducts->get($index % max(1, $allProducts->count()));
            
            $vendor = Vendor::where('name', $data['vendor_name'])->first()
                ?? $allVendors->get($index % max(1, $allVendors->count()));

            if (!$product || !$vendor) {
                continue;
            }

            $totalPrice = $data['quantity'] * $data['unit_price'];
            $due = max(0, $totalPrice - $data['payment']);

            Purchase::firstOrCreate(
                ['purchase_no' => $purchaseNo],
                [
                    'product_id'  => $product->id,
                    'vendor_id'   => $vendor->id,
                    'quantity'    => $data['quantity'],
                    'unit_price'  => $data['unit_price'],
                    'sub_price'   => $totalPrice,
                    'total_price' => $totalPrice,
                    'payment'     => $data['payment'],
                    'due'         => $due,
                    'created_by'  => $adminId,
                    'updated_by'  => $adminId,
                    'created_at'  => $data['created_at'],
                    'updated_at'  => $data['created_at'],
                ]
            );
        }
    }
}
