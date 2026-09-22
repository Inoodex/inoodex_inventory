<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds for 10 demo sales.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin?->id ?? 1;

        $allCustomers = Customer::all();
        $allProducts = Product::all();

        if ($allCustomers->isEmpty() || $allProducts->isEmpty()) {
            return;
        }

        $salesData = [
            [
                'order_no'         => 'INV-20260101-0001',
                'customer_name'    => 'Rahman & Brothers Corporate',
                'product_model'    => 'GA403UI-QS024W', // ASUS ROG G14
                'unit_price'       => 215000.00,
                'qty'              => 1,
                'discount'         => 5000.00,
                'advanced_payment' => 210000.00,
                'status'           => 'paid',
                'payment_method'   => 'bank_transfer',
                'created_at'       => now()->subDays(20),
            ],
            [
                'order_no'         => 'INV-20260102-0002',
                'customer_name'    => 'TechMatrix Solutions Ltd',
                'product_model'    => 'LAT-5540-I7', // Dell Latitude 5540
                'unit_price'       => 128000.00,
                'qty'              => 2,
                'discount'         => 0.00,
                'advanced_payment' => 150000.00,
                'status'           => 'partial',
                'payment_method'   => 'card',
                'created_at'       => now()->subDays(18),
            ],
            [
                'order_no'         => 'INV-20260103-0003',
                'customer_name'    => 'Blue Ocean Software Inc',
                'product_model'    => 'MXD13LL/A', // Apple MacBook Air 15"
                'unit_price'       => 195000.00,
                'qty'              => 1,
                'discount'         => 0.00,
                'advanced_payment' => 195000.00,
                'status'           => 'paid',
                'payment_method'   => 'bank_transfer',
                'created_at'       => now()->subDays(16),
            ],
            [
                'order_no'         => 'INV-20260104-0004',
                'customer_name'    => 'CyberSoft Technologies',
                'product_model'    => 'PB-450-G10', // HP ProBook 450
                'unit_price'       => 98000.00,
                'qty'              => 2,
                'discount'         => 6000.00,
                'advanced_payment' => 190000.00,
                'status'           => 'paid',
                'payment_method'   => 'cash',
                'created_at'       => now()->subDays(14),
            ],
            [
                'order_no'         => 'INV-20260105-0005',
                'customer_name'    => 'Prime Financial Consultants',
                'product_model'    => 'LS28BG700EWXXL', // Samsung Odyssey G7 Monitor
                'unit_price'       => 74000.00,
                'qty'              => 2,
                'discount'         => 0.00,
                'advanced_payment' => 0.00,
                'status'           => 'credit',
                'payment_method'   => 'cash',
                'created_at'       => now()->subDays(12),
            ],
            [
                'order_no'         => 'INV-20260106-0006',
                'customer_name'    => 'Apex Digital Agency',
                'product_model'    => '27UP850N-W', // LG UltraFine 27"
                'unit_price'       => 56000.00,
                'qty'              => 1,
                'discount'         => 1000.00,
                'advanced_payment' => 55000.00,
                'status'           => 'paid',
                'payment_method'   => 'card',
                'created_at'       => now()->subDays(10),
            ],
            [
                'order_no'         => 'INV-20260107-0007',
                'customer_name'    => 'Dhaka Media & Communications',
                'product_model'    => 'BX8071514700K', // Intel i7-14700K
                'unit_price'       => 48000.00,
                'qty'              => 2,
                'discount'         => 0.00,
                'advanced_payment' => 50000.00,
                'status'           => 'partial',
                'payment_method'   => 'bank_transfer',
                'created_at'       => now()->subDays(8),
            ],
            [
                'order_no'         => 'INV-20260108-0008',
                'customer_name'    => 'Vertex Engineering & Design',
                'product_model'    => '100-100000910WOF', // Ryzen 7 7800X3D
                'unit_price'       => 52000.00,
                'qty'              => 1,
                'discount'         => 2000.00,
                'advanced_payment' => 50000.00,
                'status'           => 'paid',
                'payment_method'   => 'cash',
                'created_at'       => now()->subDays(6),
            ],
            [
                'order_no'         => 'INV-20260109-0009',
                'customer_name'    => 'NexGen IT Solutions',
                'product_model'    => 'MZ-V9P2T0B/AM', // Samsung 990 PRO 2TB
                'unit_price'       => 22500.00,
                'qty'              => 3,
                'discount'         => 0.00,
                'advanced_payment' => 40000.00,
                'status'           => 'partial',
                'payment_method'   => 'card',
                'created_at'       => now()->subDays(4),
            ],
            [
                'order_no'         => 'INV-20260110-0010',
                'customer_name'    => 'Green Delta Trading Co.',
                'product_model'    => 'C1000-24T-4G-L', // Cisco Catalyst Switch
                'unit_price'       => 62000.00,
                'qty'              => 1,
                'discount'         => 0.00,
                'advanced_payment' => 62000.00,
                'status'           => 'paid',
                'payment_method'   => 'bank_transfer',
                'created_at'       => now()->subDays(1),
            ],
        ];

        foreach ($salesData as $index => $data) {
            $customer = Customer::where('name', $data['customer_name'])->first()
                ?? $allCustomers->get($index % max(1, $allCustomers->count()));

            $product = Product::where('model', $data['product_model'])->first()
                ?? $allProducts->get($index % max(1, $allProducts->count()));

            if (!$customer || !$product) {
                continue;
            }

            $total = $data['qty'] * $data['unit_price'];
            $discount = $data['discount'] ?? 0;
            $payble = max(0, $total - $discount);
            $advanced = min($payble, $data['advanced_payment']);
            $due = max(0, $payble - $advanced);

            $sale = Sale::firstOrCreate(
                ['order_no' => $data['order_no']],
                [
                    'customer_id'      => $customer->id,
                    'product_id'       => $product->id,
                    'sale_type'        => 'retail',
                    'qty'              => $data['qty'],
                    'total'            => $total,
                    'payble'           => $payble,
                    'bill'             => $total,
                    'discount'         => $discount,
                    'advanced_payment' => $advanced,
                    'due_payment'      => $due,
                    'sales_by'         => (string) $adminId,
                    'status'           => $data['status'],
                    'vat'              => 0.00,
                    'tax'              => 0.00,
                    'delivery_charge'  => 0.00,
                    'created_at'       => $data['created_at'],
                    'updated_at'       => $data['created_at'],
                ]
            );

            // Seed Sales Item
            SalesItem::firstOrCreate(
                [
                    'order_id'   => $sale->id,
                    'product_id' => $product->id,
                ],
                [
                    'unit_price'   => $data['unit_price'],
                    'warranty'     => $product->warranty ?? 365,
                    'qty'          => $data['qty'],
                    'total_price'  => $total,
                    'returned_qty' => 0,
                    'created_at'   => $data['created_at'],
                    'updated_at'   => $data['created_at'],
                ]
            );

            // Seed Payment if payment was made
            if ($advanced > 0) {
                Payment::firstOrCreate(
                    [
                        'sale_id'     => $sale->id,
                        'customer_id' => $customer->id,
                    ],
                    [
                        'payment_for'    => 2, // 2 = Sale
                        'payment_method' => $data['payment_method'] ?? 'cash',
                        'amount'         => $advanced,
                        'remarks'        => 'Initial payment for ' . $data['order_no'],
                        'created_by'     => $adminId,
                        'updated_by'     => $adminId,
                        'status'         => '1',
                        'created_at'     => $data['created_at'],
                        'updated_at'     => $data['created_at'],
                    ]
                );
            }
        }
    }
}
