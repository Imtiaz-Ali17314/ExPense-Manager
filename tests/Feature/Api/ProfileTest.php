<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('authenticated user can update profile and bank details via api', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson('/api/profile', [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '+923001234567',
            'bank_name' => 'Meezan Bank',
            'account_title' => 'Updated Name',
            'account_number' => '12345678901234',
            'iban' => 'PK36MEZN0001234567890123',
        ]);

    $response->assertOk()
        ->assertJsonPath('user.name', 'Updated Name')
        ->assertJsonPath('user.bank_name', 'Meezan Bank')
        ->assertJsonPath('user.iban', 'PK36MEZN0001234567890123');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'bank_name' => 'Meezan Bank',
        'iban' => 'PK36MEZN0001234567890123',
    ]);
});

test('authenticated user can update password via api', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword123'),
    ]);
    $token = $user->createToken('test_token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson('/api/profile/password', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Password updated successfully',
        ]);

    expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
});
