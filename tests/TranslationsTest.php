<?php

use Illuminate\Support\Arr;

/*
 * Every translation key the package asks for must exist in every locale it
 * ships — a missing one renders as the raw key, e.g.
 * "happenv-filament-user-profile::default.profile.password_confirm.current_password".
 */

function usedTranslationKeys(): array
{
    $keys = [];
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__DIR__).'/src'));

    foreach ([...$files, ...new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__DIR__).'/resources/views'))] as $file) {
        if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.php')) {
            continue;
        }

        preg_match_all("/happenv-filament-user-profile::default\.([a-z0-9_.]+)/", file_get_contents($file->getPathname()), $matches);
        array_push($keys, ...$matches[1]);
    }

    return array_values(array_unique($keys));
}

dataset('locales', fn (): array => array_map(basename(...), glob(dirname(__DIR__).'/resources/lang/*', GLOB_ONLYDIR)));

it('translates every key the package uses', function (string $locale): void {
    $translations = require dirname(__DIR__)."/resources/lang/{$locale}/default.php";

    $missing = array_values(array_filter(usedTranslationKeys(), fn (string $key): bool => ! Arr::has($translations, $key)));

    expect(usedTranslationKeys())->not->toBeEmpty()
        ->and($missing)->toBe([], "{$locale} is missing: ".implode(', ', $missing));
})->with('locales');
