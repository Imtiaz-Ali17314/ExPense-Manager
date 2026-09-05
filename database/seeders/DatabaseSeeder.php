<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Item;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Fixed Test User create karein
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // 2. Sample Vendors aur Items create karein
        $vendors = Vendor::factory(5)->create();
        $items = Item::factory(10)->create();

        // 3. Test User ke liye 10 Bills create karein
        Bill::factory(10)->create([
            'user_id' => $user->id,
        ])->each(function (Bill $bill) use ($items, $vendors) {
            // 2 se 4 random items attach karein
            $randomItems = $items->random(rand(2, 4));
            $subtotal = 0;

            foreach ($randomItems as $item) {
                $qty = rand(1, 5);
                $price = $item->current_price;
                $totalPrice = round($qty * $price, 2);
                $subtotal += $totalPrice;

                BillItem::factory()->create([
                    'bill_id' => $bill->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $totalPrice,
                ]);
            }

            // Bill ka vendor, subtotal aur grand_total update karein
            $bill->update([
                'vendor_id' => $vendors->random()->id,
                'subtotal' => $subtotal,
                'grand_total' => $subtotal,
            ]);
        });
    }
}
