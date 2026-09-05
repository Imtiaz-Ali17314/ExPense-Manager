<?php

use App\Models\User;
use App\Models\Vendor;

test('authenticated user can list vendors via api', function () {
    $user = User::factory()->create();
    Vendor::factory(3)->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/vendors');

    $response->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);
});

test('authenticated user can create vendor via api', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/vendors', [
            'name' => 'Metro Cash & Carry',
            'phone' => '+923009876543',
            'email' => 'metro@example.com',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Metro Cash & Carry');

    $this->assertDatabaseHas('vendors', [
        'name' => 'Metro Cash & Carry',
    ]);
});

test('authenticated user can update vendor via api', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create(['name' => 'Old Name']);
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson('/api/vendors/'.$vendor->id, [
            'name' => 'New Vendor Name',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'New Vendor Name');
});

test('authenticated user can delete vendor via api', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->deleteJson('/api/vendors/'.$vendor->id);

    $response->assertOk();
    $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
});
