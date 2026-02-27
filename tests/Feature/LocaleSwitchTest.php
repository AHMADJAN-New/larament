<?php

declare(strict_types=1);

test('locale switch route stores pashto locale in session', function () {
    $response = $this->from('/admin')->get(route('locale.switch', ['locale' => 'ps']));

    $response->assertRedirect('/admin');
    $response->assertSessionHas('locale', 'ps');
    $response->assertSessionHas('locale_manually_selected', true);
});

test('locale switch route rejects unsupported locale', function () {
    $response = $this->get(route('locale.switch', ['locale' => 'fr']));

    $response->assertNotFound();
});
