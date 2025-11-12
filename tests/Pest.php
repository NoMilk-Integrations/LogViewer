<?php

pest()->beforeEach(function () {
    foreach ($this->files as $key => $file) {
        $this->files[$key] = storage_path('logs/' . $file . '.log');
    }

    $lines = [
        '['.now()->subWeeks(10)->format('Y-m-d').' 12:00:00] old log',
        '['.now()->subWeeks(2)->format('Y-m-d').' 12:00:00] recent log',
    ];

    foreach ($this->files as $file) {
        File::put($file, implode("\n", $lines) . "\n");
    }
});

pest()->afterEach(function () {
    foreach ($this->files as $file) {
        if (File::exists($file)) {
            File::delete($file);
        }
    }
});

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}