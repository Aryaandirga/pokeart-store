<?php

test('checkout controller does not start with utf-8 bom', function () {
    $path = app_path('Http/Controllers/CheckoutController.php');

    expect(file_exists($path))->toBeTrue();

    $head = file_get_contents($path, false, null, 0, 3);

    expect($head)->not->toBe("\xEF\xBB\xBF");
});
