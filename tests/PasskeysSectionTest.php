<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Happenv\FilamentUserProfile\Livewire\Passkeys;
use Happenv\FilamentUserProfile\UserProfilePlugin;
use Livewire\Exceptions\ComponentNotFoundException;
use Livewire\Livewire;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;

/**
 * spatie/laravel-passkeys is only suggested. The suite runs twice in CI: once with
 * it installed (require-dev) and once after `composer remove`, so each path below is
 * proven against the real presence or absence of the package, not a simulated one.
 */
function profilePluginOnPanel(): UserProfilePlugin
{
    $plugin = UserProfilePlugin::make();

    $panel = Panel::make()
        ->id('passkeys-section')
        ->path('passkeys-section')
        ->plugin($plugin);

    Filament::registerPanel(fn (): Panel => $panel);
    Filament::setCurrentPanel($panel);

    return $plugin;
}

/**
 * `Livewire::exists()` is Livewire 4 only; `new()` resolves the name on 3 and 4 alike.
 */
function livewireResolves(string $name): bool
{
    try {
        Livewire::new($name);
    } catch (ComponentNotFoundException) {
        return false;
    }

    return true;
}

function profileScripts(): array
{
    return array_map(
        fn ($script): string => $script->getId(),
        FilamentAsset::getScripts(['happenv/filament-user-profile'], withCore: false),
    );
}

it('offers the passkeys section when spatie/laravel-passkeys is installed', function () {
    $plugin = profilePluginOnPanel();

    expect(UserProfilePlugin::passkeysAreAvailable())->toBeTrue()
        ->and($plugin->getProfileComponents())->toHaveKey('passkeys', Passkeys::class)
        ->and($plugin->getRegisteredMyProfileComponents()->all())->toContain(Passkeys::class)
        ->and(livewireResolves('passkeys'))->toBeTrue()
        ->and(profileScripts())->toContain('filament-passkeys-scripts');
})->skip(! class_exists(PasskeysComponent::class), 'spatie/laravel-passkeys is not installed.');

it('leaves the passkeys section out without spatie/laravel-passkeys', function () {
    $plugin = profilePluginOnPanel();

    expect(UserProfilePlugin::passkeysAreAvailable())->toBeFalse()
        ->and($plugin->getProfileComponents())->not->toHaveKey('passkeys')
        ->and($plugin->getRegisteredMyProfileComponents()->all())->not->toContain(Passkeys::class)
        ->and(livewireResolves('passkeys'))->toBeFalse()
        ->and(profileScripts())->not->toContain('filament-passkeys-scripts');
})->skip(class_exists(PasskeysComponent::class), 'spatie/laravel-passkeys is installed.');

it('keeps leaving the section out when the host registers it explicitly without spatie/laravel-passkeys', function () {
    $plugin = profilePluginOnPanel()->registerProfileComponent('passkeys', Passkeys::class);

    expect($plugin->getProfileComponents())->not->toHaveKey('passkeys');
})->skip(class_exists(PasskeysComponent::class), 'spatie/laravel-passkeys is installed.');
