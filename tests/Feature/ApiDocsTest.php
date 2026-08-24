<?php

test('can access swagger api docs UI page', function () {
    $response = $this->get('/api/docs');

    $response->assertStatus(200)
        ->assertSee('SwaggerUIBundle');
});

test('can fetch swagger api docs json specification', function () {
    $response = $this->get('/api/docs/json');

    $response->assertStatus(200)
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonPath('openapi', '3.0.3');
});
