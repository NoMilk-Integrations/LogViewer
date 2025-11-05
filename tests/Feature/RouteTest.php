<?php declare(strict_types=1);

describe('Routes', function () {
    it('has accessible route when enabled', function () {
        $response = $this->get('/log');

        expect($response->status())->not->toBe(404);
    });
});
