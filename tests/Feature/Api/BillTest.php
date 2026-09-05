<?php

use App\Models\Bill;
use App\Models\Item;
use App\Models\User;
use App\Models\Vendor;

test('authenticated user can list their bills via api', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    Bill::factory(3)->create(['user_id' => $user->id, 'vendor_id' => $vendor->id]);

    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/bills');

    $response->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);
});

test('authenticated user can create a bill with items via api', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $item1 = Item::factory()->create(['current_price' => 100]);
    $item2 = Item::factory()->create(['current_price' => 200]);

    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/bills', [
            'vendor_id' => $vendor->id,
            'bill_number' => 'BILL-2026-001',
            'bill_date' => '2026-09-05',
            'status' => 'paid',
            'items' => [
                ['item_id' => $item1->id, 'quantity' => 2, 'unit_price' => 100],
                ['item_id' => $item2->id, 'quantity' => 1, 'unit_price' => 200],
            ],
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.bill_number', 'BILL-2026-001')
        ->assertJsonPath('data.subtotal', 400)
        ->assertJsonPath('data.grand_total', 400);

    $this->assertDatabaseHas('bills', [
        'user_id' => $user->id,
        'bill_number' => 'BILL-2026-001',
        'subtotal' => 400,
    ]);

    $this->assertDatabaseHas('bill_items', [
        'item_id' => $item1->id,
        'quantity' => 2,
        'total_price' => 200,
    ]);
});

test('authenticated user can show single bill details via api', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $bill = Bill::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor->id]);

    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/bills/'.$bill->id);

    $response->assertOk()
        ->assertJsonPath('data.id', $bill->id);
});

test('authenticated user cannot view another user bill', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $bill = Bill::factory()->create(['user_id' => $user1->id]);

    $token = $user2->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/bills/'.$bill->id);

    $response->assertStatus(403);
});

test('authenticated user can delete their bill via api', function () {
    $user = User::factory()->create();
    $bill = Bill::factory()->create(['user_id' => $user->id]);

    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->deleteJson('/api/bills/'.$bill->id);

    $response->assertOk();
    $this->assertDatabaseMissing('bills', ['id' => $bill->id]);
});
