<?php

test('the application returns a successful response', function () {
    config(['commerce.domains.storefront' => 'http://localhost:3000']);

    $response = $this->get('/');

    $response->assertRedirect('http://localhost:3000');
});

test('the local root redirect preserves lan host for the next storefront', function () {
    $this
        ->get('http://192.168.1.107:8000/')
        ->assertRedirect('http://192.168.1.107:3000');
});
