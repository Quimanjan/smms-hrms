<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    expect(auth()->user()->name)->toBe('Test User');
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration combines middle name and suffix when provided', function () {
    $this->post('/register', [
        'first_name' => 'Test',
        'middle_name' => 'Middle',
        'last_name' => 'User',
        'suffix' => 'Jr.',
        'email' => 'test-middle@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    expect(auth()->user()->name)->toBe('Test Middle User, Jr.');
});
