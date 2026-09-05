<?php

use App\Models\Item;
use App\Models\User;

test('authenticated user can list items via api', function () {
    $user = User::factory()->create();
    Item::factory(3)->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/items');

    $response->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);
});

test('authenticated user can create item via api', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/items', [
            'name' => 'Cooking Oil 5L',
            'unit' => 'bottle',
            'current_price' => 2500.00,
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Cooking Oil 5L')
        ->assertJsonPath('data.current_price', 2500);

    $this->assertDatabaseHas('items', [
        'name' => 'Cooking Oil 5L',
    ]);
});

test('updating item price automatically tracks price history', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create([
        'name' => 'Sugar',
        'current_price' => 100.00,
    ]);
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson('/api/items/'.$item->id, [
            'name' => 'Sugar',
            'current_price' => 120.00,
        ]);

    $response->assertOk()
        ->assertJsonPath('data.current_price', 120)
        ->assertJsonPath('data.previous_price', 100)
        ->assertJsonPath('data.average_price', 110);
});

test('authenticated user can delete item via api', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->deleteJson('/api/items/'.$item->id);

    $response->assertOk();
    $this->assertDatabaseMissing('items', ['id' => $item->id]);
});
